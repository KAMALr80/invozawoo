<td style="padding: 10px;">
    <div style="display: flex; gap: 8px; justify-content: flex-start; align-items: center; flex-wrap: wrap;">
        
        {{-- VIEW (DETAIL PAGE) --}}
        <a href="{{ route('sales.view', $sale->id) }}" 
           style="display: inline-flex; align-items: center; gap: 4px; padding: 6px 12px; 
                  border-radius: 6px; font-size: 13px; font-weight: 500; text-decoration: none; 
                  background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; 
                  transition: all 0.2s ease; white-space: nowrap;"
           onmouseover="this.style.background='#bae6fd'; this.style.transform='translateY(-1px)'"
           onmouseout="this.style.background='#e0f2fe'; this.style.transform='translateY(0)'"
           title="View Invoice Details">
            <span style="font-size: 14px;">👁️</span>
            <span>View</span>
        </a>

        {{-- PRINT --}}
        <a href="{{ route('sales.invoice', $sale->id) }}" target="_blank" 
           style="display: inline-flex; align-items: center; gap: 4px; padding: 6px 12px; 
                  border-radius: 6px; font-size: 13px; font-weight: 500; text-decoration: none; 
                  background: #dcfce7; color: #166534; border: 1px solid #86efac; 
                  transition: all 0.2s ease; white-space: nowrap;"
           onmouseover="this.style.background='#bbf7d0'; this.style.transform='translateY(-1px)'"
           onmouseout="this.style.background='#dcfce7'; this.style.transform='translateY(0)'"
           title="Print Invoice">
            <span style="font-size: 14px;">🖨️</span>
            <span>Invoice</span>
        </a>

        {{-- EDIT --}}
        <a href="{{ route('sales.edit', $sale->id) }}" 
           style="display: inline-flex; align-items: center; gap: 4px; padding: 6px 12px; 
                  border-radius: 6px; font-size: 13px; font-weight: 500; text-decoration: none; 
                  background: #fef3c7; color: #92400e; border: 1px solid #fde68a; 
                  transition: all 0.2s ease; white-space: nowrap;"
           onmouseover="this.style.background='#fde68a'; this.style.transform='translateY(-1px)'"
           onmouseout="this.style.background='#fef3c7'; this.style.transform='translateY(0)'"
           title="Edit Invoice">
            <span style="font-size: 14px;">✏️</span>
            <span>Edit</span>
        </a>
    </div>

    {{-- Responsive Inline Styles --}}
    <style>
        /* Responsive adjustments for the action buttons container */
        @media (max-width: 768px) {
            td div[style*="display: flex"] {
                gap: 6px;
            }
            td a[style*="padding: 6px 12px"] {
                padding: 5px 10px;
                font-size: 12px;
            }
        }

        @media (max-width: 575px) {
            td div[style*="display: flex"] {
                flex-direction: column;
                gap: 5px;
                width: 100%;
            }
            td a[style*="padding: 6px 12px"] {
                width: 100%;
                justify-content: center;
                padding: 8px 12px;
                font-size: 13px;
            }
        }

        @media (max-width: 360px) {
            td a[style*="padding: 6px 12px"] {
                padding: 6px 10px;
                font-size: 12px;
            }
        }

        @media print {
            td div[style*="display: flex"] {
                display: none !important;
            }
        }
    </style>
</td>