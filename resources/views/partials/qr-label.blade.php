@php
    $material = $record;
@endphp
<div id="printable-label" class="font-sans">
    <style>
        @media print {
            @page {
                size: A6 portrait;
                margin: 8mm;
            }
            body * {
                visibility: hidden;
            }
            #printable-label, #printable-label * {
                visibility: visible;
            }
            #printable-label {
                position: fixed;
                left: 0;
                top: 0;
                width: 105mm;
                height: 148mm;
                margin: 0;
                padding: 8mm;
                background: white;
            }
            .no-print {
                display: none !important;
            }
            .qr-container {
                text-align: center;
            }
            .qr-code-img {
                width: 70mm !important;
                height: 70mm !important;
            }
        }

        .qr-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 1.5rem;
            background: white;
        }
        .qr-code-img {
            width: 200px;
            height: 200px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            padding: 8px;
            background: white;
        }
        .material-info {
            margin-top: 1.5rem;
            text-align: center;
            width: 100%;
            max-width: 300px;
        }
        .material-name {
            font-size: 1.25rem;
            font-weight: 700;
            color: #111827;
            margin-bottom: 0.5rem;
        }
        .material-id {
            font-family: monospace;
            font-size: 0.75rem;
            color: #6b7280;
            background: #f3f4f6;
            padding: 0.25rem 0.75rem;
            border-radius: 4px;
            margin-bottom: 1rem;
            word-break: break-all;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.75rem;
            text-align: left;
            font-size: 0.875rem;
        }
        .info-item {
            padding: 0.5rem;
            background: #f9fafb;
            border-radius: 4px;
        }
        .info-label {
            font-size: 0.625rem;
            text-transform: uppercase;
            color: #9ca3af;
            letter-spacing: 0.05em;
        }
        .info-value {
            font-weight: 600;
            color: #374151;
            margin-top: 2px;
        }
        .print-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-weight: 600;
            font-size: 0.875rem;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        .print-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 10px -1px rgba(0, 0, 0, 0.15);
        }
        .print-btn svg {
            width: 1.25rem;
            height: 1.25rem;
        }
        .paper-note {
            font-size: 0.75rem;
            color: #9ca3af;
            margin-top: 0.5rem;
        }
    </style>

    <div class="qr-container">
        <!-- Big QR Code -->
        <img 
            src="https://api.qrserver.com/v1/create-qr-code/?size=300x300&data={{ $material->unique_id }}&margin=0" 
            alt="QR Code" 
            class="qr-code-img"
        >
        
        <!-- Material Info -->
        <div class="material-info">
            <div class="material-name">{{ $material->material_name }}</div>
            <div class="material-id">{{ $material->unique_id }}</div>
            
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Lab PO No.</div>
                    <div class="info-value">{{ $material->lab_po_number ?: '-' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Supplier</div>
                    <div class="info-value">{{ $material->supplier ?: '-' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Lot No.</div>
                    <div class="info-value">{{ $material->lot_number ?: '-' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Quantity</div>
                    <div class="info-value">{{ $material->qty ?: '-' }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Print Button -->
    <div class="flex flex-col items-center mt-6 no-print">
        <button type="button" onclick="window.print()" class="print-btn">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5zm-3 0h.008v.008H15V10.5z" />
            </svg>
            Print A6 Label
        </button>
        <div class="paper-note">Optimized for A6 paper (105 × 148 mm)</div>
    </div>
</div>
