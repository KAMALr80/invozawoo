@extends('layouts.app')

@section('content')
    <div
        style="max-width: 600px; margin: 40px auto; background: #ffffff; padding: 40px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08); font-family: 'Inter', 'Segoe UI', sans-serif;">

        {{-- HEADER --}}
        <div style="margin-bottom: 30px;">
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 8px;">
                <div
                    style="width: 48px; height: 48px; background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                    <span style="font-size: 24px; color: white;">📆</span>
                </div>
                <div>
                    <h2 style="margin: 0; font-size: 28px; font-weight: 800; color: #1f2937; letter-spacing: -0.5px;">
                        Pay EMI
                    </h2>
                    <p style="margin: 4px 0 0 0; color: #6b7280; font-size: 15px;">
                        Invoice #{{ $emi->sale->invoice_no }}
                    </p>
                </div>
            </div>
        </div>

        {{-- EMI DETAILS CARD --}}
        @if ($emi->sale->payment_status === 'emi' && $emi->sale->emiPlan)
            <div
                style="background: #f8fafc; padding: 24px; border-radius: 16px; margin-bottom: 30px; border: 1px solid #e5e7eb;">
                <h3
                    style="margin: 0 0 20px 0; font-size: 18px; font-weight: 700; color: #374151; display: flex; align-items: center; gap: 10px;">
                    <span style="background: #e0e7ff; padding: 6px; border-radius: 8px;">📆</span>
                    EMI Details
                </h3>

                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px;">
                    <div>
                        <div style="color: #6b7280; font-size: 14px; margin-bottom: 4px;">Total Amount</div>
                        <div style="font-size: 18px; font-weight: 700; color: #1f2937;">₹
                            {{ number_format($emi->sale->emiPlan->total_amount, 2) }}</div>
                    </div>
                    <div>
                        <div style="color: #6b7280; font-size: 14px; margin-bottom: 4px;">Down Payment</div>
                        <div style="font-size: 18px; font-weight: 700; color: #059669;">₹
                            {{ number_format($emi->sale->emiPlan->down_payment, 2) }}</div>
                    </div>
                    <div>
                        <div style="color: #6b7280; font-size: 14px; margin-bottom: 4px;">Monthly EMI</div>
                        <div style="font-size: 18px; font-weight: 700; color: #4f46e5;">₹
                            {{ number_format($emi->sale->emiPlan->emi_amount, 2) }}</div>
                    </div>
                    <div>
                        <div style="color: #6b7280; font-size: 14px; margin-bottom: 4px;">Status</div>
                        <div style="font-size: 18px; font-weight: 700; color: #f59e0b; text-transform: uppercase;">
                            {{ $emi->sale->emiPlan->status }}
                        </div>
                    </div>
                </div>

                @php
                    $remaining = $emi->sale->emiPlan->emi_amount * $emi->sale->emiPlan->months;
                @endphp

                <div style="margin-top: 20px; padding-top: 20px; border-top: 2px dashed #e5e7eb;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <div style="color: #6b7280; font-size: 14px; margin-bottom: 4px;">Remaining Balance</div>
                            <div style="font-size: 24px; font-weight: 800; color: #dc2626;">₹
                                {{ number_format($remaining, 2) }}</div>
                        </div>
                        <div
                            style="background: #fef3c7; padding: 8px 16px; border-radius: 20px; color: #92400e; font-weight: 600; font-size: 14px;">
                            {{ $emi->sale->emiPlan->months }} months remaining
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- ADVANCE BALANCE NOTICE --}}
        @if (isset($openBalance) && $openBalance < 0)
            <div
                style="background: linear-gradient(135deg, #ecfeff 0%, #dbeafe 100%); padding: 20px; border-radius: 16px; margin-bottom: 30px; border: 1px solid #bae6fd;">
                <div style="display: flex; align-items: flex-start; gap: 12px;">
                    <div style="background: #0ea5e9; color: white; padding: 8px; border-radius: 10px; font-size: 20px;">
                        💡
                    </div>
                    <div>
                        <div style="font-weight: 700; color: #0369a1; margin-bottom: 4px;">
                            Advance Balance Available
                        </div>
                        <div style="color: #0c4a6e; font-size: 18px; font-weight: 800; margin-bottom: 8px;">
                            ₹ {{ number_format(abs($openBalance), 2) }}
                        </div>
                        <div style="color: #64748b; font-size: 14px;">
                            This amount will be automatically adjusted against your EMI payment.
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- PAYMENT FORM --}}
        <div
            style="background: linear-gradient(135deg, #ffffff 0%, #fefefe 100%); padding: 30px; border-radius: 16px; border: 1px solid #e5e7eb; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);">
            <h3
                style="margin: 0 0 24px 0; font-size: 20px; font-weight: 700; color: #374151; display: flex; align-items: center; gap: 10px;">
                <span style="background: #dbeafe; padding: 6px; border-radius: 8px;">💳</span>
                Make Payment
            </h3>

            <form method="POST" action="{{ route('emi.pay', $emi->id) }}">
                @csrf

                <div style="margin-bottom: 24px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #4b5563; font-size: 15px;">
                        Payment Amount
                    </label>
                    <div style="position: relative;">
                        <input type="number" name="amount" value="{{ $payAmount }}" readonly
                            style="width: 100%;
                                  padding: 16px 20px;
                                  font-size: 18px;
                                  font-weight: 700;
                                  color: #059669;
                                  background: #f0fdf4;
                                  border: 2px solid #bbf7d0;
                                  border-radius: 12px;
                                  text-align: center;
                                  font-family: 'JetBrains Mono', monospace;">
                        <div
                            style="position: absolute; right: 20px; top: 50%; transform: translateY(-50%); color: #059669; font-weight: 700;">
                            ₹
                        </div>
                    </div>
                    <div style="margin-top: 8px; color: #6b7280; font-size: 14px; text-align: center;">
                        This is your monthly EMI amount
                    </div>
                </div>

                <button type="submit"
                    style="width: 100%;
                           padding: 18px;
                           background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
                           color: white;
                           border: none;
                           border-radius: 14px;
                           font-weight: 800;
                           font-size: 18px;
                           cursor: pointer;
                           transition: all 0.3s ease;
                           display: flex;
                           align-items: center;
                           justify-content: center;
                           gap: 12px;"
                    onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 10px 25px rgba(79, 70, 229, 0.3)';"
                    onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                    <span style="font-size: 22px;">💳</span>
                    Pay ₹ {{ number_format($payAmount, 2) }}
                </button>

                <div style="margin-top: 16px; text-align: center; color: #9ca3af; font-size: 13px;">
                    Clicking will process the payment immediately
                </div>
            </form>
        </div>

        {{-- BACK LINK --}}
        <div style="margin-top: 30px; text-align: center;">
            <a href="{{ route('sales.show', $emi->sale_id) }}"
                style="display: inline-flex;
                  align-items: center;
                  gap: 8px;
                  color: #6b7280;
                  text-decoration: none;
                  font-weight: 600;
                  font-size: 15px;
                  padding: 12px 24px;
                  border-radius: 10px;
                  transition: all 0.3s ease;"
                onmouseover="this.style.color='#374151'; this.style.backgroundColor='#f9fafb';"
                onmouseout="this.style.color='#6b7280'; this.style.backgroundColor='transparent';">
                <span style="font-size: 18px;">←</span>
                Back to Invoice Details
            </a>
        </div>

        {{-- DISCLAIMER --}}
        <div
            style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #f1f5f9; text-align: center; color: #9ca3af; font-size: 13px;">
            All payments are securely processed • EMI payments are non-refundable
        </div>
    </div>

    <style>
        @media (max-width: 640px) {
            div[style*="max-width: 600px"] {
                margin: 20px auto;
                padding: 24px;
            }

            div[style*="grid-template-columns: repeat(2, 1fr)"] {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endsection
