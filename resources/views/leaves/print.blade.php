<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leave Application - {{ $leave->leave_number ?? 'N/A' }}</title>
    <style>
        /* ================= PRINT OPTIMIZED STYLES ================= */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: white;
            color: #333;
            line-height: 1.5;
            padding: 20px;
        }

        .print-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border: 1px solid #ddd;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        /* ================= HEADER ================= */
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #007bff;
        }

        .company-name {
            font-size: 24px;
            font-weight: 700;
            color: #007bff;
            margin-bottom: 5px;
        }

        .document-title {
            font-size: 20px;
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }

        .leave-number {
            font-family: monospace;
            font-size: 16px;
            color: #666;
            background: #f5f5f5;
            padding: 5px 15px;
            border-radius: 20px;
            display: inline-block;
        }

        /* ================= STATUS BADGE ================= */
        .status-badge {
            display: inline-block;
            padding: 8px 20px;
            border-radius: 30px;
            font-weight: 600;
            font-size: 14px;
            margin-top: 10px;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeeba;
        }

        .status-approved {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .status-rejected {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .status-cancelled {
            background: #e9ecef;
            color: #495057;
            border: 1px solid #ced4da;
        }

        /* ================= EMPLOYEE INFO ================= */
        .employee-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            border: 1px solid #dee2e6;
        }

        .section-title {
            font-size: 16px;
            font-weight: 700;
            color: #007bff;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 2px solid #007bff;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }

        .info-item {
            display: flex;
            flex-direction: column;
        }

        .info-label {
            font-size: 12px;
            color: #666;
            margin-bottom: 3px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .info-value {
            font-size: 15px;
            font-weight: 600;
            color: #333;
        }

        /* ================= LEAVE DETAILS ================= */
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }

        .details-table th {
            background: #007bff;
            color: white;
            padding: 12px;
            text-align: left;
            font-size: 14px;
            font-weight: 600;
        }

        .details-table td {
            padding: 12px;
            border: 1px solid #dee2e6;
            font-size: 14px;
        }

        .details-table tr:last-child td {
            border-bottom: 2px solid #007bff;
        }

        .details-table .label {
            font-weight: 600;
            background: #f8f9fa;
            width: 30%;
        }

        /* ================= REASON SECTION ================= */
        .reason-section {
            margin-bottom: 25px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #dee2e6;
        }

        .reason-text {
            font-size: 14px;
            line-height: 1.7;
            margin-top: 10px;
            padding: 15px;
            background: white;
            border-radius: 6px;
            border: 1px solid #dee2e6;
        }

        /* ================= HANDOVER SECTION ================= */
        .handover-section {
            margin-bottom: 25px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #dee2e6;
        }

        .handover-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-top: 15px;
        }

        .handover-item {
            background: white;
            padding: 15px;
            border-radius: 6px;
            border: 1px solid #dee2e6;
        }

        .handover-label {
            font-size: 12px;
            color: #666;
            margin-bottom: 5px;
            text-transform: uppercase;
        }

        .handover-value {
            font-size: 14px;
            font-weight: 600;
            color: #333;
        }

        /* ================= APPROVAL SECTION ================= */
        .approval-section {
            margin-bottom: 25px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #dee2e6;
        }

        .approval-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-top: 15px;
        }

        .approval-item {
            background: white;
            padding: 15px;
            border-radius: 6px;
            border: 1px solid #dee2e6;
        }

        .approval-status {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            margin-top: 8px;
        }

        .approval-status.approved {
            background: #d4edda;
            color: #155724;
        }

        .approval-status.rejected {
            background: #f8d7da;
            color: #721c24;
        }

        /* ================= SIGNATURE SECTION ================= */
        .signature-section {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
            padding: 20px 0;
            border-top: 2px dashed #dee2e6;
        }

        .signature-box {
            text-align: center;
            width: 200px;
        }

        .signature-line {
            margin-top: 40px;
            padding-top: 10px;
            border-top: 1px solid #333;
            font-size: 14px;
            font-weight: 600;
        }

        .signature-label {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }

        /* ================= FOOTER ================= */
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
            font-size: 12px;
            color: #666;
            text-align: center;
        }

        .footer p {
            margin: 5px 0;
        }

        /* ================= PRINT MEDIA ================= */
        @media print {
            body {
                padding: 0;
                background: white;
            }

            .print-container {
                box-shadow: none;
                border: none;
                padding: 0;
            }

            .status-badge {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .details-table th {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .approval-status {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .no-print {
                display: none;
            }
        }

        /* ================= UTILITIES ================= */
        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .mt-20 {
            margin-top: 20px;
        }

        .mb-10 {
            margin-bottom: 10px;
        }

        .no-print {
            margin-top: 20px;
            text-align: center;
        }

        .btn-print {
            padding: 10px 30px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-print:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <div class="print-container">
        <!-- Header -->
        <div class="header">
            <div class="company-name">{{ config('app.name') }}</div>
            <div class="document-title">LEAVE APPLICATION FORM</div>
            <div class="leave-number">{{ $leave->leave_number ?? 'N/A' }}</div>
            <div class="status-badge status-{{ $leave->status }}">
                @if($leave->status == 'pending') ⏳ PENDING
                @elseif($leave->status == 'approved') ✅ APPROVED
                @elseif($leave->status == 'rejected') ❌ REJECTED
                @elseif($leave->status == 'cancelled') ↩️ CANCELLED
                @endif
            </div>
        </div>

        <!-- Employee Information -->
        <div class="employee-section">
            <div class="section-title">EMPLOYEE INFORMATION</div>
            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">Employee Name</span>
                    <span class="info-value">{{ $leave->employee->name ?? 'N/A' }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Employee Code</span>
                    <span class="info-value">{{ $leave->employee->employee_code ?? 'N/A' }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Department</span>
                    <span class="info-value">{{ $leave->employee->department ?? 'N/A' }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Designation</span>
                    <span class="info-value">{{ $leave->employee->designation ?? 'N/A' }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Email</span>
                    <span class="info-value">{{ $leave->employee->email ?? 'N/A' }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Phone</span>
                    <span class="info-value">{{ $leave->employee->phone ?? 'N/A' }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Joining Date</span>
                    <span class="info-value">{{ $leave->employee->joining_date ? \Carbon\Carbon::parse($leave->employee->joining_date)->format('d M Y') : 'N/A' }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Applied On</span>
                    <span class="info-value">{{ $leave->applied_on ? $leave->applied_on->format('d M Y, h:i A') : 'N/A' }}</span>
                </div>
            </div>
        </div>

        <!-- Leave Details Table -->
        <table class="details-table">
            <tr>
                <th colspan="2">LEAVE DETAILS</th>
            </tr>
            <tr>
                <td class="label">Leave Type</td>
                <td>{{ $leave->leave_type_label ?? $leave->leave_type }}</td>
            </tr>
            <tr>
                <td class="label">Duration Type</td>
                <td>{{ $leave->duration_label ?? $leave->duration_type }}</td>
            </tr>
            @if($leave->session)
            <tr>
                <td class="label">Session</td>
                <td>{{ $leave->session == 'first_half' ? 'First Half (9:00 AM - 1:00 PM)' : 'Second Half (2:00 PM - 6:00 PM)' }}</td>
            </tr>
            @endif
            @if($leave->start_time && $leave->end_time)
            <tr>
                <td class="label">Time</td>
                <td>{{ \Carbon\Carbon::parse($leave->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($leave->end_time)->format('h:i A') }}</td>
            </tr>
            @endif
            <tr>
                <td class="label">From Date</td>
                <td>{{ \Carbon\Carbon::parse($leave->from_date)->format('l, d F Y') }}</td>
            </tr>
            <tr>
                <td class="label">To Date</td>
                <td>{{ \Carbon\Carbon::parse($leave->to_date)->format('l, d F Y') }}</td>
            </tr>
            <tr>
                <td class="label">Total Days</td>
                <td><strong>{{ $leave->total_days ?? 1 }}</strong> day(s)</td>
            </tr>
            @if($leave->contact_number)
            <tr>
                <td class="label">Contact Number</td>
                <td>{{ $leave->contact_number }}</td>
            </tr>
            @endif
        </table>

        <!-- Reason for Leave -->
        <div class="reason-section">
            <div class="section-title">REASON FOR LEAVE</div>
            <div class="reason-text">
                {{ $leave->reason ?? 'No reason provided' }}
            </div>
        </div>

        <!-- Work Handover -->
        @if($leave->handover_notes || $leave->handover_person || $leave->alternate_arrangements)
        <div class="handover-section">
            <div class="section-title">WORK HANDOVER</div>
            <div class="handover-grid">
                @if($leave->handover_person)
                <div class="handover-item">
                    <div class="handover-label">Handover Person</div>
                    <div class="handover-value">{{ $leave->handover_person }}</div>
                </div>
                @endif
                @if($leave->handover_notes)
                <div class="handover-item">
                    <div class="handover-label">Handover Notes</div>
                    <div class="handover-value">{{ $leave->handover_notes }}</div>
                </div>
                @endif
                @if($leave->alternate_arrangements)
                <div class="handover-item" style="grid-column: span 2;">
                    <div class="handover-label">Alternate Arrangements</div>
                    <div class="handover-value">{{ $leave->alternate_arrangements }}</div>
                </div>
                @endif
                @if($leave->emergency_contact)
                <div class="handover-item">
                    <div class="handover-label">Emergency Contact</div>
                    <div class="handover-value">{{ $leave->emergency_contact }}</div>
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Approval/Rejection Details -->
        @if($leave->status == 'approved' || $leave->status == 'rejected' || $leave->status == 'cancelled')
        <div class="approval-section">
            <div class="section-title">APPROVAL DETAILS</div>
            <div class="approval-grid">
                @if($leave->status == 'approved')
                <div class="approval-item">
                    <div class="handover-label">Approved By</div>
                    <div class="handover-value">{{ $leave->approver->name ?? 'Admin' }}</div>
                    <div class="handover-label mt-20">Approved On</div>
                    <div class="handover-value">{{ $leave->approved_at ? $leave->approved_at->format('d M Y, h:i A') : 'N/A' }}</div>
                    <div class="approval-status approved">✅ APPROVED</div>
                </div>
                @endif

                @if($leave->status == 'rejected')
                <div class="approval-item">
                    <div class="handover-label">Rejected By</div>
                    <div class="handover-value">{{ $leave->rejector->name ?? 'Admin' }}</div>
                    <div class="handover-label mt-20">Rejected On</div>
                    <div class="handover-value">{{ $leave->rejected_at ? $leave->rejected_at->format('d M Y, h:i A') : 'N/A' }}</div>
                    <div class="handover-label mt-20">Rejection Reason</div>
                    <div class="handover-value">{{ $leave->rejection_reason ?? 'N/A' }}</div>
                    <div class="approval-status rejected">❌ REJECTED</div>
                </div>
                @endif

                @if($leave->status == 'cancelled')
                <div class="approval-item">
                    <div class="handover-label">Cancelled By</div>
                    <div class="handover-value">{{ $leave->canceller->name ?? 'Employee' }}</div>
                    <div class="handover-label mt-20">Cancelled On</div>
                    <div class="handover-value">{{ $leave->cancelled_at ? $leave->cancelled_at->format('d M Y, h:i A') : 'N/A' }}</div>
                    @if($leave->cancellation_reason)
                    <div class="handover-label mt-20">Cancellation Reason</div>
                    <div class="handover-value">{{ $leave->cancellation_reason }}</div>
                    @endif
                    <div class="approval-status rejected">↩️ CANCELLED</div>
                </div>
                @endif

                @if($leave->approval_remarks)
                <div class="approval-item" style="grid-column: span 2;">
                    <div class="handover-label">Approval Remarks</div>
                    <div class="handover-value">{{ $leave->approval_remarks }}</div>
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Signature Section -->
        <div class="signature-section">
            <div class="signature-box">
                <div class="signature-line">__________________</div>
                <div class="signature-label">Employee Signature</div>
                <div style="font-size: 12px; margin-top: 5px;">{{ $leave->employee->name ?? '' }}</div>
            </div>
            <div class="signature-box">
                <div class="signature-line">__________________</div>
                <div class="signature-label">Manager/HR Signature</div>
                @if($leave->status == 'approved')
                <div style="font-size: 12px; margin-top: 5px;">{{ $leave->approver->name ?? '' }}</div>
                @endif
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>This is a computer generated document. No signature is required.</p>
            <p>Generated on: {{ now()->format('d F Y, h:i A') }}</p>
            <p>Leave Number: {{ $leave->leave_number ?? 'N/A' }}</p>
        </div>

        <!-- Print Button (hidden in print) -->
        <div class="no-print">
            <button class="btn-print" onclick="window.print()">
                🖨️ Print / Save as PDF
            </button>
            <p style="margin-top: 10px; color: #666; font-size: 12px;">
                Click print and save as PDF for digital copy
            </p>
        </div>
    </div>

    <script>
        // Auto-trigger print dialog (optional - remove if not needed)
        // window.onload = function() {
        //     window.print();
        // };
    </script>
</body>
</html>
