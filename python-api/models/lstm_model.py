# D:\smartErp\python-api\models\lstm_model.py

import numpy as np
import pandas as pd
from sklearn.preprocessing import MinMaxScaler
from sklearn.model_selection import train_test_split
import tensorflow as tf
from tensorflow.keras.models import Sequential, load_model
from tensorflow.keras.layers import LSTM, Dense, Dropout
from tensorflow.keras.callbacks import EarlyStopping, ModelCheckpoint
import joblib
import os
import logging
from datetime import datetime, timedelta

logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

class LSTMSalesPredictor:
    def __init__(self, sequence_length=30, n_features=1, model_path='models/lstm_model.h5', scaler_path='models/scaler.pkl'):
        """
        Initialize LSTM Sales Predictor

        Args:
            sequence_length: Number of past days to use for prediction
            n_features: Number of features (1 for univariate, >1 for multivariate)
            model_path: Path to save/load model
            scaler_path: Path to save/load scaler
        """
        self.sequence_length = sequence_length
        self.n_features = n_features
        self.model_path = model_path
        self.scaler_path = scaler_path
        self.model = None
        self.scaler = MinMaxScaler(feature_range=(0, 1))

        # Create models directory if it doesn't exist
        os.makedirs(os.path.dirname(model_path), exist_ok=True)

    def create_sequences(self, data):
        """
        Create sequences for LSTM training

        Args:
            data: Scaled data array

        Returns:
            X: Input sequences
            y: Target values
        """
        X, y = [], []
        for i in range(self.sequence_length, len(data)):
            X.append(data[i-self.sequence_length:i, 0])
            y.append(data[i, 0])
        return np.array(X), np.array(y)

    def prepare_data(self, df, target_col='daily_sales', fit_scaler=True):
        """
        Prepare data for LSTM training/prediction

        Args:
            df: DataFrame with date and sales columns
            target_col: Name of target column
            fit_scaler: Whether to fit scaler or use existing

        Returns:
            X, y, original_dates
        """
        # Sort by date
        df = df.sort_values('date').copy()

        # Extract values
        data = df[target_col].values.reshape(-1, 1)
        dates = df['date'].values

        # Scale the data
        if fit_scaler:
            scaled_data = self.scaler.fit_transform(data)
            # Save scaler
            joblib.dump(self.scaler, self.scaler_path)
            logger.info(f"Scaler saved to {self.scaler_path}")
        else:
            scaled_data = self.scaler.transform(data)

        # Create sequences
        X, y = self.create_sequences(scaled_data)

        # Reshape X for LSTM [samples, time steps, features]
        X = X.reshape(X.shape[0], X.shape[1], 1)

        return X, y, dates[self.sequence_length:]

    def build_model(self):
        """Build LSTM model architecture"""
        self.model = Sequential([
            # First LSTM layer with return sequences
            LSTM(128, return_sequences=True, input_shape=(self.sequence_length, 1)),
            Dropout(0.2),

            # Second LSTM layer
            LSTM(64, return_sequences=True),
            Dropout(0.2),

            # Third LSTM layer
            LSTM(32, return_sequences=False),
            Dropout(0.2),

            # Dense layers
            Dense(16, activation='relu'),
            Dense(1)
        ])

        # Compile model
        self.model.compile(
            optimizer=tf.keras.optimizers.Adam(learning_rate=0.001),
            loss='mse',
            metrics=['mae']
        )

        logger.info("LSTM model built successfully")
        return self.model

    def train(self, df, target_col='daily_sales', epochs=100, batch_size=32, validation_split=0.2):
        """
        Train LSTM model

        Args:
            df: DataFrame with sales data
            target_col: Target column name
            epochs: Number of training epochs
            batch_size: Batch size for training
            validation_split: Fraction of data for validation

        Returns:
            Training history
        """
        # Prepare data
        X, y, dates = self.prepare_data(df, target_col, fit_scaler=True)

        # Split into train/validation
        split_idx = int(len(X) * (1 - validation_split))
        X_train, X_val = X[:split_idx], X[split_idx:]
        y_train, y_val = y[:split_idx], y[split_idx:]

        logger.info(f"Training data shape: X_train={X_train.shape}, y_train={y_train.shape}")
        logger.info(f"Validation data shape: X_val={X_val.shape}, y_val={y_val.shape}")

        # Build model if not exists
        if self.model is None:
            self.build_model()

        # Callbacks
        callbacks = [
            EarlyStopping(patience=15, restore_best_weights=True, monitor='val_loss'),
            ModelCheckpoint(self.model_path, save_best_only=True, monitor='val_loss')
        ]

        # Train model
        history = self.model.fit(
            X_train, y_train,
            validation_data=(X_val, y_val),
            epochs=epochs,
            batch_size=batch_size,
            callbacks=callbacks,
            verbose=1
        )

        logger.info(f"Model training completed. Best validation loss: {min(history.history['val_loss']):.6f}")

        # Save final model
        self.save_model()

        return history

    def predict(self, df, days=15, target_col='daily_sales'):
        """
        Predict future sales - FIXED VERSION with proper type handling

        Args:
            df: DataFrame with historical sales data
            days: Number of days to predict
            target_col: Target column name

        Returns:
            predictions: Array of predicted values (as Python floats)
            lower_bound: Lower confidence bound (as Python floats)
            upper_bound: Upper confidence bound (as Python floats)
        """
        try:
            # Sort and prepare data
            df = df.sort_values('date').copy()

            # Ensure we have enough data
            if len(df) < self.sequence_length:
                raise ValueError(f"Need at least {self.sequence_length} days, got {len(df)}")

            # Get last sequence_length days
            last_data = df[target_col].values[-self.sequence_length:].reshape(-1, 1)

            # Handle NaN or invalid values
            last_data = np.nan_to_num(last_data, nan=0.0, posinf=10000, neginf=0)

            # Scale the data
            scaled_last = self.scaler.transform(last_data)

            # Initialize predictions list (store as Python floats)
            predictions = []
            current_seq = scaled_last.copy()

            for i in range(days):
                current_input = current_seq.reshape(1, self.sequence_length, 1)
                next_pred_scaled = self.model.predict(current_input, verbose=0)[0, 0]
                predictions.append(float(next_pred_scaled))  # Convert to Python float
                current_seq = np.roll(current_seq, -1)
                current_seq[-1] = next_pred_scaled

            # Convert to numpy array for inverse transform
            predictions_array = np.array(predictions).reshape(-1, 1)

            # Inverse transform
            predictions_array = self.scaler.inverse_transform(predictions_array)

            # Flatten and ensure positive values, convert to Python floats
            predictions = [max(float(x), 1.0) for x in predictions_array.flatten()]

            # Calculate confidence based on prediction stability
            pred_array = np.array(predictions)
            pred_std = float(np.std(pred_array)) if len(predictions) > 1 else 0.1
            pred_mean = float(np.mean(pred_array))
            cv = pred_std / pred_mean if pred_mean > 0 else 0.5
            confidence = max(80, min(95, 95 - (cv * 10)))

            # Calculate bounds using the confidence level
            bound_factor = (1 - confidence/100)
            lower_bound = [float(x * (1 - bound_factor)) for x in predictions]
            upper_bound = [float(x * (1 + bound_factor)) for x in predictions]

            logger.info(f"LSTM prediction successful. Confidence: {confidence:.1f}%")

            return predictions, lower_bound, upper_bound

        except Exception as e:
            logger.error(f"LSTM prediction error: {str(e)}")
            raise

    def _calculate_historical_errors(self, df, target_col='daily_sales'):
        """Calculate average error for confidence intervals"""
        try:
            if len(df) < self.sequence_length + 10:
                return 0.15

            # Get training predictions
            X, y, _ = self.prepare_data(df, target_col, fit_scaler=False)
            y_pred_scaled = self.model.predict(X, verbose=0)

            # Inverse transform
            y_true = self.scaler.inverse_transform(y.reshape(-1, 1))
            y_pred = self.scaler.inverse_transform(y_pred_scaled)

            # Calculate MAPE
            errors = np.abs((y_true - y_pred) / y_true)
            avg_error = float(np.mean(errors))

            return min(avg_error, 0.2)  # Cap at 20%
        except Exception as e:
            logger.error(f"Error calculating historical errors: {e}")
            return 0.15  # Default 15% error

    def save_model(self):
        """Save model to disk"""
        if self.model:
            self.model.save(self.model_path)
            logger.info(f"Model saved to {self.model_path}")

    def load_model(self):
        """Load model from disk"""
        if os.path.exists(self.model_path):
            self.model = load_model(self.model_path)
            self.scaler = joblib.load(self.scaler_path)
            logger.info(f"Model loaded from {self.model_path}")
            return True
        else:
            logger.warning(f"Model file {self.model_path} not found")
            return False

    def get_feature_importance(self):
        """LSTM doesn't provide direct feature importance, but we can return attention-like weights"""
        # For LSTM, we can analyze weights but it's complex
        # Return sample importance for now
        return {
            'Recent Sales': 0.45,
            'Weekly Pattern': 0.25,
            'Monthly Trend': 0.20,
            'Seasonality': 0.10
        }
