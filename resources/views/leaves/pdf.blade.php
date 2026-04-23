<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leave Application - {{ $leave->leave_number ?? 'N/A' }}</title>
    <style>
        /* ================= PDF OPTIMIZED STYLES ================= */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', 'Segoe UI', Arial, sans-serif;
            background: white;
            color: #333;
            line-height: 1.5;
            font-size: 12px;
        }

        .pdf-container {
            max-width: 100%;
            margin: 0;
            padding: 20px;
            background: white;
        }

        /* ================= HEADER ================= */
        .header {
            text-align: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 3px solid #007bff;
        }

        .company-name {
            font-size: 22px;
            font-weight: 700;
            color: #007bff;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .document-title {
            font-size: 18px;
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }

        .leave-number {
            font-family: monospace;
            font-size: 14px;
            color: #666;
            background: #f5f5f5;
            padding: 4px 12px;
            border-radius: 20px;
            display: inline-block;
            margin-bottom: 8px;
        }

        /* ================= STATUS BADGE ================= */
        .status-badge {
            display: inline-block;
            padding: 6px 16px;
            border-radius: 25px;
            font-weight: 600;
            font-size: 12px;
            margin-top: 5px;
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
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            border: 1px solid #dee2e6;
        }

        .section-title {
            font-size: 14px;
            font-weight: 700;
            color: #007bff;
            margin-bottom: 12px;
            padding-bottom: 6px;
            border-bottom: 2px solid #007bff;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .info-grid {
            display: table;
            width: 100%;
            border-collapse: collapse;
        }

        .info-row {
            display: table-row;
        }

        .info-cell {
            display: table-cell;
            padding: 8px 10px;
            border-bottom: 1px dashed #dee2e6;
        }

        .info-label {
            font-weight: 600;
            color: #666;
            width: 30%;
            font-size: 11px;
            text-transform: uppercase;
        }

        .info-value {
            font-weight: 500;
            color: #333;
            width: 70%;
            font-size: 12px;
        }

        /* ================= LEAVE DETAILS ================= */
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            border: 1px solid #dee2e6;
        }

        .details-table th {
            background: #007bff;
            color: white;
            padding: 10px;
            text-align: left;
            font-size: 13px;
            font-weight: 600;
        }

        .details-table td {
            padding: 10px;
            border: 1px solid #dee2e6;
            font-size: 12px;
        }

        .details-table .label {
            font-weight: 600;
            background: #f8f9fa;
            width: 35%;
        }

        /* ================= REASON SECTION ================= */
        .reason-section {
            margin-bottom: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 6px;
            border: 1px solid #dee2e6;
        }

        .reason-text {
            font-size: 12px;
            line-height: 1.6;
            margin-top: 8px;
            padding: 12px;
            background: white;
            border-radius: 4px;
            border: 1px solid #dee2e6;
        }

        /* ================= HANDOVER SECTION ================= */
        .handover-section {
            margin-bottom: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 6px;
            border: 1px solid #dee2e6;
        }

        .handover-grid {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .handover-grid td {
            padding: 8px;
            vertical-align: top;
        }

        .handover-item {
            background: white;
            padding: 12px;
            border-radius: 4px;
            border: 1px solid #dee2e6;
            height: 100%;
        }

        .handover-label {
            font-size: 10px;
            color: #666;
            margin-bottom: 4px;
            text-transform: uppercase;
            font-weight: 600;
        }

        .handover-value {
            font-size: 11px;
            font-weight: 500;
            color: #333;
            line-height: 1.4;
        }

        /* ================= APPROVAL SECTION ================= */
        .approval-section {
            margin-bottom: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 6px;
            border: 1px solid #dee2e6;
        }

        .approval-grid {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .approval-grid td {
            padding: 8px;
            vertical-align: top;
        }

        .approval-item {
            background: white;
            padding: 12px;
            border-radius: 4px;
            border: 1px solid #dee2e6;
        }

        .approval-status {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 15px;
            font-size: 10px;
            font-weight: 600;
            margin-top: 6px;
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
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px dashed #dee2e6;
            width: 100%;
            border-collapse: collapse;
        }

        .signature-box {
            text-align: center;
            width: 45%;
        }

        .signature-line {
            margin-top: 35px;
            padding-top: 5px;
            border-top: 1px solid #333;
            font-size: 11px;
            font-weight: 600;
        }

        .signature-label {
            font-size: 9px;
            color: #666;
            margin-top: 3px;
            text-transform: uppercase;
        }

        .signature-name {
            font-size: 10px;
            margin-top: 3px;
            color: #333;
        }

        /* ================= FOOTER ================= */
        .footer {
            margin-top: 25px;
            padding-top: 15px;
            border-top: 1px solid #dee2e6;
            font-size: 9px;
            color: #666;
            text-align: center;
        }

        .footer p {
            margin: 3px 0;
        }

        /* ================= WATERMARK ================= */
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 60px;
            opacity: 0.03;
            color: #007bff;
            font-weight: 700;
            white-space: nowrap;
            z-index: -1;
            pointer-events: none;
        }

        /* ================= UTILITIES ================= */
        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .mt-10 {
            margin-top: 10px;
        }

        .mb-5 {
            margin-bottom: 5px;
        }

        .fw-600 {
            font-weight: 600;
        }

        .text-primary {
            color: #007bff;
        }

        .bg-light {
            background: #f8f9fa;
        }

        .border {
            border: 1px solid #dee2e6;
        }

        .rounded {
            border-radius: 4px;
        }

        .p-10 {
            padding: 10px;
        }
    </style>
</head>
<body>
    <div class="pdf-container">
        <!-- Watermark -->
        <div class="watermark">{{ config('app.name') }}</div>

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
                <div class="info-row">
                    <div class="info-cell info-label">Employee Name</div>
                    <div class="info-cell info-value">{{ $leave->employee->name ?? 'N/A' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-cell info-label">Employee Code</div>
                    <div class="info-cell info-value">{{ $leave->employee->employee_code ?? 'N/A' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-cell info-label">Department</div>
                    <div class="info-cell info-value">{{ $leave->employee->department ?? 'N/A' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-cell info-label">Designation</div>
                    <div class="info-cell info-value">{{ $leave->employee->designation ?? 'N/A' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-cell info-label">Email</div>
                    <div class="info-cell info-value">{{ $leave->employee->email ?? 'N/A' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-cell info-label">Phone</div>
                    <div class="info-cell info-value">{{ $leave->employee->phone ?? 'N/A' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-cell info-label">Joining Date</div>
                    <div class="info-cell info-value">{{ $leave->employee->joining_date ? \Carbon\Carbon::parse($leave->employee->joining_date)->format('d M Y') : 'N/A' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-cell info-label">Applied On</div>
                    <div class="info-cell info-value">{{ $leave->applied_on ? $leave->applied_on->format('d M Y, h:i A') : 'N/A' }}</div>
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
            @if($leave->emergency_contact)
            <tr>
                <td class="label">Emergency Contact</td>
                <td>{{ $leave->emergency_contact }}</td>
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
            <table class="handover-grid">
                <tr>
                    @if($leave->handover_person)
                    <td style="width: 50%;">
                        <div class="handover-item">
                            <div class="handover-label">Handover Person</div>
                            <div class="handover-value">{{ $leave->handover_person }}</div>
                        </div>
                    </td>
                    @endif
                    @if($leave->emergency_contact && !$leave->handover_person)
                    <td style="width: 50%;">
                        <div class="handover-item">
                            <div class="handover-label">Emergency Contact</div>
                            <div class="handover-value">{{ $leave->emergency_contact }}</div>
                        </div>
                    </td>
                    @endif
                </tr>
                @if($leave->handover_notes)
                <tr>
                    <td colspan="2">
                        <div class="handover-item">
                            <div class="handover-label">Handover Notes</div>
                            <div class="handover-value">{{ $leave->handover_notes }}</div>
                        </div>
                    </td>
                </tr>
                @endif
                @if($leave->alternate_arrangements)
                <tr>
                    <td colspan="2">
                        <div class="handover-item">
                            <div class="handover-label">Alternate Arrangements</div>
                            <div class="handover-value">{{ $leave->alternate_arrangements }}</div>
                        </div>
                    </td>
                </tr>
                @endif
            </table>
        </div>
        @endif

        <!-- Approval/Rejection Details -->
        @if($leave->status == 'approved' || $leave->status == 'rejected' || $leave->status == 'cancelled')
        <div class="approval-section">
            <div class="section-title">APPROVAL DETAILS</div>
            <table class="approval-grid">
                <tr>
                    @if($leave->status == 'approved')
                    <td style="width: 50%;">
                        <div class="approval-item">
                            <div class="handover-label">Approved By</div>
                            <div class="handover-value">{{ $leave->approver->name ?? 'Admin' }}</div>
                            <div class="handover-label" style="margin-top: 8px;">Approved On</div>
                            <div class="handover-value">{{ $leave->approved_at ? $leave->approved_at->format('d M Y, h:i A') : 'N/A' }}</div>
                            <div class="approval-status approved">APPROVED</div>
                        </div>
                    </td>
                    @endif

                    @if($leave->status == 'rejected')
                    <td style="width: 50%;">
                        <div class="approval-item">
                            <div class="handover-label">Rejected By</div>
                            <div class="handover-value">{{ $leave->rejector->name ?? 'Admin' }}</div>
                            <div class="handover-label" style="margin-top: 8px;">Rejected On</div>
                            <div class="handover-value">{{ $leave->rejected_at ? $leave->rejected_at->format('d M Y, h:i A') : 'N/A' }}</div>
                            <div class="handover-label" style="margin-top: 8px;">Rejection Reason</div>
                            <div class="handover-value">{{ $leave->rejection_reason ?? 'N/A' }}</div>
                            <div class="approval-status rejected">REJECTED</div>
                        </div>
                    </td>
                    @endif

                    @if($leave->status == 'cancelled')
                    <td style="width: 50%;">
                        <div class="approval-item">
                            <div class="handover-label">Cancelled By</div>
                            <div class="handover-value">{{ $leave->canceller->name ?? 'Employee' }}</div>
                            <div class="handover-label" style="margin-top: 8px;">Cancelled On</div>
                            <div class="handover-value">{{ $leave->cancelled_at ? $leave->cancelled_at->format('d M Y, h:i A') : 'N/A' }}</div>
                            @if($leave->cancellation_reason)
                            <div class="handover-label" style="margin-top: 8px;">Cancellation Reason</div>
                            <div class="handover-value">{{ $leave->cancellation_reason }}</div>
                            @endif
                            <div class="approval-status rejected">CANCELLED</div>
                        </div>
                    </td>
                    @endif
                </tr>
                @if($leave->approval_remarks)
                <tr>
                    <td colspan="2">
                        <div class="approval-item">
                            <div class="handover-label">Approval Remarks</div>
                            <div class="handover-value">{{ $leave->approval_remarks }}</div>
                        </div>
                    </td>
                </tr>
                @endif
            </table>
        </div>
        @endif

        <!-- Signature Section -->
        <table class="signature-section">
            <tr>
                <td class="signature-box" style="width: 45%;">
                    <div class="signature-line">_________________________</div>
                    <div class="signature-label">Employee Signature</div>
                    <div class="signature-name">{{ $leave->employee->name ?? '' }}</div>
                </td>
                <td style="width: 10%;"></td>
                <td class="signature-box" style="width: 45%;">
                    <div class="signature-line">_________________________</div>
                    <div class="signature-label">Manager/HR Signature</div>
                    @if($leave->status == 'approved')
                    <div class="signature-name">{{ $leave->approver->name ?? '' }}</div>
                    @endif
                </td>
            </tr>
        </table>

        <!-- Footer -->
        <div class="footer">
            <p>This is a computer generated document. No signature is required.</p>
            <p>Generated on: {{ now()->format('d F Y, h:i A') }}</p>
            <p>Leave Number: {{ $leave->leave_number ?? 'N/A' }}</p>
            <p>{{ config('app.name') }} - Leave Management System</p>
        </div>
    </div>
</body>
</html>
