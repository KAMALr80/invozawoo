# D:\smartErp\python-api\models\train_model.py

import sys
import os
sys.path.append(os.path.dirname(os.path.dirname(os.path.abspath(__file__))))

from models.lstm_model import LSTMSalesPredictor
import pandas as pd
import mysql.connector
from datetime import datetime, timedelta
import logging

logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

def fetch_training_data():
    """Fetch historical sales data for training"""
    try:
        conn = mysql.connector.connect(
            host="localhost",
            user="root",
            password="",
            database="smarterp1"
        )

        query = """
            SELECT
                DATE(sale_date) as date,
                SUM(grand_total) as daily_sales,
                COUNT(*) as transaction_count,
                AVG(grand_total) as avg_transaction
            FROM sales
            WHERE sale_date >= CURDATE() - INTERVAL 2 YEAR
                AND (deleted_at IS NULL OR deleted_at = '0000-00-00 00:00:00')
            GROUP BY DATE(sale_date)
            ORDER BY date ASC
        """

        df = pd.read_sql(query, conn)
        conn.close()

        # Fill missing dates with 0
        df['date'] = pd.to_datetime(df['date'])
        date_range = pd.date_range(start=df['date'].min(), end=df['date'].max(), freq='D')
        df = df.set_index('date').reindex(date_range).fillna(0).rename_axis('date').reset_index()

        logger.info(f"Fetched {len(df)} days of training data")
        return df

    except Exception as e:
        logger.error(f"Error fetching training data: {e}")
        return None

def train_model():
    """Main training function"""
    logger.info("Starting LSTM model training...")

    # Fetch data
    df = fetch_training_data()
    if df is None or len(df) < 60:
        logger.error("Insufficient training data")
        return False

    # Initialize predictor
    predictor = LSTMSalesPredictor(
        sequence_length=30,
        model_path='models/lstm_model.h5',
        scaler_path='models/scaler.pkl'
    )

    # Train model
    history = predictor.train(
        df=df,
        target_col='daily_sales',
        epochs=100,
        batch_size=32,
        validation_split=0.2
    )

    # Save model
    predictor.save_model()

    logger.info("Model training completed successfully")
    return True

if __name__ == "__main__":
    train_model()
