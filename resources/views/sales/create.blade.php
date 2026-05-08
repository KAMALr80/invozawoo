@extends('layouts.app')

@section('page-title', 'Professional POS')

@section('content')
    @php
        use Illuminate\Support\Str;
    @endphp

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="google-maps-api-key" content="{{ config('services.google.maps_api_key') }}">

    <div class="h-screen flex flex-col bg-slate-100 overflow-hidden font-sans">
        {{-- Header Bar --}}
        <x-pos.header />

        <div class="flex-1 flex overflow-hidden">
            {{-- Main Content Area --}}
            <div class="flex-1 flex flex-col overflow-hidden px-4 py-3">
                <div class="flex flex-col h-full overflow-hidden">
                    {{-- Customer & Product Search Row --}}
                    <div class="grid grid-cols-12 gap-4 mb-4 flex-shrink-0">
                        <div class="col-span-12 lg:col-span-4">
                            <x-pos.customer-section />
                        </div>
                        <div class="col-span-12 lg:col-span-8">
                            <x-pos.product-search :categories="$categories" />
                        </div>
                    </div>

                    {{-- Table Area - Should take all remaining space --}}
                    <div class="flex-1 overflow-hidden min-h-0 bg-white rounded-xl border border-gray-200 shadow-sm">
                        <x-pos.table />
                    </div>

                    {{-- Summary Section (Bottom) --}}
                    <div class="mt-4 flex-shrink-0">
                        <x-pos.summary />
                    </div>
                </div>
            </div>

            {{-- Right Sidebar --}}
            <x-pos.sidebar :categories="$categories" />
        </div>

        {{-- Footer Actions --}}
        <x-pos.footer />
    </div>

    {{-- Hidden Form for submission --}}
    <form method="POST" action="{{ route('sales.store') }}" id="invoiceForm" class="hidden">
        @csrf
        <input type="hidden" name="invoice_token" id="invoice_token_field">
        <input type="hidden" name="customer_id" id="customer_id">
        <input type="hidden" name="sub_total_mrp" id="sub_total_mrp" value="0.00">
        <input type="hidden" name="total_discount" id="total_discount" value="0.00">
        <input type="hidden" name="sub_total" id="sub_total" value="0.00">
        <input type="hidden" name="tax" id="tax" value="0">
        <input type="hidden" name="tax_amount" id="tax_amount" value="0.00">
        <input type="hidden" name="grand_total" id="grand_total" value="0.00">
        
        {{-- Shipping Hidden Fields --}}
        <input type="hidden" name="requires_shipping" id="requires_shipping_hidden" value="0">
        <input type="hidden" name="shipping_address" id="shipping_address">
        <input type="hidden" name="city" id="city">
        <input type="hidden" name="state" id="state">
        <input type="hidden" name="pincode" id="pincode">
        <input type="hidden" name="destination_latitude" id="destination_latitude">
        <input type="hidden" name="destination_longitude" id="destination_longitude">
        <input type="hidden" name="place_id" id="place_id">
        <input type="hidden" name="receiver_name" id="receiver_name">
        <input type="hidden" name="receiver_phone" id="receiver_phone">
        <input type="hidden" name="delivery_instructions" id="delivery_instructions">
        
        <div id="hiddenItemsContainer"></div>
    </form>

    {{-- Customer Modal --}}
    <div id="customerModal" class="fixed inset-0 z-[100] flex items-center justify-center bg-black/60 backdrop-blur-sm hidden">
        <div class="bg-white w-full max-w-lg rounded-2xl shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-300">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-gray-50">
                <h3 class="font-bold text-gray-800">Add New Customer</h3>
                <button type="button" onclick="InvoiceManager.closeCustomerModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-[11px] font-bold text-gray-400 uppercase mb-1">Full Name *</label>
                    <input id="c_name" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[11px] font-bold text-gray-400 uppercase mb-1">Mobile *</label>
                        <input id="c_mobile" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-gray-400 uppercase mb-1">Email</label>
                        <input id="c_email" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all">
                    </div>
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-gray-400 uppercase mb-1">Address</label>
                    <textarea id="c_address" rows="3" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all resize-none"></textarea>
                </div>
            </div>
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end space-x-3">
                <button onclick="InvoiceManager.closeCustomerModal()" class="px-4 py-2 text-xs font-bold text-gray-500 uppercase hover:bg-gray-100 rounded-lg transition-all">Cancel</button>
                <button onclick="InvoiceManager.saveCustomer()" id="saveCustomerBtn" class="px-6 py-2 bg-blue-600 text-white text-xs font-bold uppercase rounded-lg shadow-md hover:bg-blue-700 transition-all">Save Customer</button>
            </div>
        </div>
    </div>

    {{-- Success Modal --}}
    <div id="successModal" class="fixed inset-0 z-[100] flex items-center justify-center bg-black/70 backdrop-blur-md hidden">
        <div class="bg-white w-full max-w-sm rounded-2xl shadow-2xl p-8 text-center animate-in fade-in slide-in-from-bottom-8 duration-500">
            <div class="w-20 h-20 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-check text-3xl"></i>
            </div>
            <h2 class="text-xl font-black text-gray-800 mb-2">Invoice Created!</h2>
            <p class="text-sm text-gray-500 mb-6" id="successMsg">Invoice has been saved successfully.</p>
            
            <div class="bg-gray-50 rounded-xl p-4 mb-6 text-left border border-gray-100">
                <div class="flex justify-between mb-1">
                    <span class="text-[10px] font-bold text-gray-400 uppercase">Invoice #</span>
                    <span class="text-xs font-bold text-gray-700" id="res_invoice_no">---</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-[10px] font-bold text-gray-400 uppercase">Amount</span>
                    <span class="text-xs font-bold text-blue-600" id="res_amount">₹ 0.00</span>
                </div>
            </div>

            <div class="space-y-3">
                <button type="button" onclick="window.print()" class="w-full py-3 bg-blue-600 text-white font-bold text-xs uppercase tracking-widest rounded-xl shadow-lg hover:bg-blue-700 transition-all active:scale-95 flex items-center justify-center">
                    <i class="fas fa-print mr-2"></i> Print Invoice
                </button>
                <button type="button" onclick="window.location.reload()" class="w-full py-3 bg-gray-100 text-gray-600 font-bold text-xs uppercase tracking-widest rounded-xl hover:bg-gray-200 transition-all active:scale-95">
                    Create New Invoice
                </button>
            </div>
        </div>
    </div>

    <script
        src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_api_key') }}&libraries=places&callback=initAddressAutocomplete"
        async defer></script>
    <script src="{{ asset('js/pos-data-service.js') }}"></script>

    <script>
        const InvoiceManager = (function() {
            let state = {
                products: @json($products),
                customers: @json($customers),
                invoice_token: "{{ $invoice_token }}",
                isCustomerSelected: false,
                isSavingCustomer: false,
                isScannerEnabled: false,
                barcodeBuffer: '',
                barcodeTimeout: null,
                customerTimer: null,
                autocompleteService: null,
                placesService: null,
                isGettingLocation: false,
                selectedCustomer: null
            };

            const elements = {
                customerSearch: document.getElementById('customerSearch'),
                customerResults: document.getElementById('customerResults'),
                customerIdInput: document.getElementById('customer_id'),
                customerStatus: document.getElementById('customerStatus'),
                selectedCustomerInfo: document.getElementById('selectedCustomerInfo'),
                selectedCustomerName: document.getElementById('selectedCustomerName'),
                selectedCustomerMobileText: document.getElementById('selectedCustomerMobileText'),
                selectedCustomerEmailText: document.getElementById('selectedCustomerEmailText'),
                productSearch: document.getElementById('productSearch'),
                productResults: document.getElementById('productResults'),
                productStatus: document.getElementById('productStatus'),
                productSearchHint: document.getElementById('productSearchHint'),
                itemsTable: document.getElementById('itemsTable'),
                emptyState: document.getElementById('emptyState'),
                barcodeInput: document.getElementById('barcodeInput'),
                customerModal: document.getElementById('customerModal'),
                saveCustomerBtn: document.getElementById('saveCustomerBtn'),
                requiresShipping: document.getElementById('requiresShipping'),
                shippingAddressAutocomplete: document.getElementById('shipping_address_autocomplete'),
                shippingAddress: document.getElementById('shipping_address'),
                city: document.getElementById('city'),
                state: document.getElementById('state'),
                pincode: document.getElementById('pincode'),
                addressSuggestions: document.getElementById('addressSuggestions'),
                destinationLatitude: document.getElementById('destination_latitude'),
                destinationLongitude: document.getElementById('destination_longitude'),
                placeId: document.getElementById('place_id'),
                productGrid: document.getElementById('productGrid'),
                subTotalSpan: document.getElementById('subTotal'),
                totalAmountSpan: document.getElementById('totalAmount'),
                itemCountSpan: document.getElementById('itemCount'),
                totalPayableSpan: document.getElementById('totalPayable')
            };

            // ========== GOOGLE MAPS AUTOCOMPLETE ==========
            function initAddressAutocomplete() {
                if (!window.google || !window.google.maps || !window.google.maps.places) return;
                state.autocompleteService = new google.maps.places.AutocompleteService();
                state.placesService = new google.maps.places.PlacesService(document.createElement('div'));
                if (elements.shippingAddressAutocomplete) {
                    elements.shippingAddressAutocomplete.addEventListener('input', handleAddressInput);
                }
            }

            let addressDebounceTimer = null;
            function handleAddressInput() {
                const input = elements.shippingAddressAutocomplete.value;
                if (input.length < 3) { elements.addressSuggestions.style.display = 'none'; return; }
                clearTimeout(addressDebounceTimer);
                addressDebounceTimer = setTimeout(() => {
                    state.autocompleteService.getPlacePredictions({
                        input: input, types: ['address'], componentRestrictions: { country: 'in' }
                    }, (predictions, status) => {
                        if (status === 'OK' && predictions) displaySuggestions(predictions);
                        else elements.addressSuggestions.style.display = 'none';
                    });
                }, 300);
            }

            function displaySuggestions(predictions) {
                elements.addressSuggestions.innerHTML = '';
                predictions.forEach(prediction => {
                    const item = document.createElement('div');
                    item.className = 'px-4 py-2 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-0';
                    item.innerHTML = `
                        <div class="text-xs font-bold text-gray-700">${escapeHTML(prediction.structured_formatting.main_text)}</div>
                        <div class="text-[10px] text-gray-400">${escapeHTML(prediction.structured_formatting.secondary_text || '')}</div>
                    `;
                    item.onclick = () => selectPlace(prediction.place_id);
                    elements.addressSuggestions.appendChild(item);
                });
                elements.addressSuggestions.style.display = 'block';
            }

            function selectPlace(placeId) {
                elements.addressSuggestions.style.display = 'none';
                elements.shippingAddressAutocomplete.value = 'Loading address...';
                state.placesService.getDetails({
                    placeId: placeId, fields: ['formatted_address', 'address_components', 'geometry']
                }, (place, status) => {
                    if (status === 'OK' && place) {
                        elements.shippingAddressAutocomplete.value = place.formatted_address;
                        if (elements.shippingAddress) elements.shippingAddress.value = place.formatted_address;
                        if (place.geometry && place.geometry.location) {
                            if (elements.destinationLatitude) elements.destinationLatitude.value = place.geometry.location.lat();
                            if (elements.destinationLongitude) elements.destinationLongitude.value = place.geometry.location.lng();
                        }
                        if (elements.placeId) elements.placeId.value = placeId;
                        extractAddressComponents(place.address_components);
                        showToast('Address fetched successfully!', 'success');
                    }
                });
            }

            function extractAddressComponents(components) {
                let city = '', stateName = '', pincode = '';
                components.forEach(component => {
                    const types = component.types;
                    if (types.includes('locality')) city = component.long_name;
                    else if (types.includes('administrative_area_level_1')) stateName = component.long_name;
                    else if (types.includes('postal_code')) pincode = component.long_name;
                });
                if (elements.city) elements.city.value = city;
                if (elements.state) elements.state.value = stateName;
                if (elements.pincode) elements.pincode.value = pincode;
            }

            // ========== LIVE LOCATION ==========
            async function getLiveLocation() {
                if (state.isGettingLocation) return;
                if (!navigator.geolocation) { showToast('Geolocation not supported', 'error'); return; }
                state.isGettingLocation = true;
                const btn = document.getElementById('shareLiveLocationBtn');
                btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Locating...';
                navigator.geolocation.getCurrentPosition(
                    async (position) => {
                        const { latitude, longitude } = position.coords;
                        await reverseGeocode(latitude, longitude);
                        btn.innerHTML = '📍 Share Live Location';
                        state.isGettingLocation = false;
                    },
                    (error) => { showToast('Location access denied', 'error'); btn.innerHTML = '📍 Share Live Location'; state.isGettingLocation = false; }
                );
            }

            async function reverseGeocode(lat, lng) {
                const geocoder = new google.maps.Geocoder();
                geocoder.geocode({ location: { lat, lng } }, (results, status) => {
                    if (status === 'OK' && results[0]) {
                        const address = results[0].formatted_address;
                        if (elements.shippingAddressAutocomplete) elements.shippingAddressAutocomplete.value = address;
                        if (elements.shippingAddress) elements.shippingAddress.value = address;
                        if (elements.destinationLatitude) elements.destinationLatitude.value = lat;
                        if (elements.destinationLongitude) elements.destinationLongitude.value = lng;
                        showToast('Location fetched!', 'success');
                    }
                });
            }

            // ========== CUSTOMER FUNCTIONS ==========
            function handleCustomerSearch() {
                const query = this.value.trim();
                clearTimeout(state.customerTimer);
                if (query.length < 2) { elements.customerResults.style.display = 'none'; return; }
                elements.customerResults.innerHTML = '<div class="p-4 text-center text-gray-400 text-xs">Searching...</div>';
                elements.customerResults.style.display = 'block';
                state.customerTimer = setTimeout(() => performCustomerSearch(query), 500);
            }

            function performCustomerSearch(query) {
                const search = query.toLowerCase();
                const customers = state.customers.filter(c => (c.name && c.name.toLowerCase().includes(search)) || (c.mobile && c.mobile.includes(search)));
                elements.customerResults.innerHTML = '';
                if (customers.length === 0) {
                    elements.customerResults.innerHTML = '<div class="p-4 text-center text-gray-400 text-xs">No customers found</div>';
                    return;
                }
                customers.forEach(customer => {
                    const item = document.createElement('div');
                    item.className = 'p-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100';
                    item.innerHTML = `
                        <div class="text-sm font-bold text-gray-800">${escapeHTML(customer.name)}</div>
                        <div class="text-[10px] text-gray-400">📱 ${escapeHTML(customer.mobile || 'N/A')}</div>
                    `;
                    item.onclick = () => selectCustomer(customer);
                    elements.customerResults.appendChild(item);
                });
            }

            function selectCustomer(customer) {
                state.selectedCustomer = customer;
                state.isCustomerSelected = true;
                if (elements.customerIdInput) elements.customerIdInput.value = customer.id;
                if (elements.customerSearch) elements.customerSearch.value = customer.name;
                if (elements.selectedCustomerName) elements.selectedCustomerName.textContent = customer.name;
                if (elements.selectedCustomerMobileText) elements.selectedCustomerMobileText.textContent = customer.mobile || '---';
                if (elements.selectedCustomerEmailText) elements.selectedCustomerEmailText.textContent = customer.email || '---';
                if (elements.selectedCustomerInfo) elements.selectedCustomerInfo.classList.remove('hidden');
                document.getElementById('customerStatus').classList.add('hidden');
                elements.customerResults.style.display = 'none';
                enableProductSearch();
                populateSidebarProducts();
                showToast(`Customer "${customer.name}" selected`, 'success');
            }

            function clearCustomerSelection() {
                state.isCustomerSelected = false;
                if (elements.customerSearch) elements.customerSearch.value = '';
                if (elements.selectedCustomerInfo) elements.selectedCustomerInfo.classList.add('hidden');
                document.getElementById('customerStatus').classList.remove('hidden');
                disableProductSearch();
                clearAllProducts();
            }

            function enableProductSearch() {
                if (elements.productSearch) {
                    elements.productSearch.disabled = false;
                    elements.productSearch.placeholder = "Enter product name / SKU / Barcode";
                }
                document.getElementById('productSearchHint').classList.add('hidden');
            }

            function disableProductSearch() {
                if (elements.productSearch) {
                    elements.productSearch.disabled = true;
                    elements.productSearch.placeholder = "First select a customer...";
                }
                document.getElementById('productSearchHint').classList.remove('hidden');
            }

            // ========== PRODUCT FUNCTIONS ==========
            function handleProductSearch() {
                const val = this.value.toLowerCase().trim();
                if (!val) { elements.productResults.style.display = 'none'; return; }

                // Check for exact SKU match
                const exactMatch = state.products.find(p => p.product_code && p.product_code.toString().toLowerCase() === val);
                if (exactMatch) {
                    if (!state.isCustomerSelected) {
                        showToast('Please select a customer first!', 'error');
                        return;
                    }
                    addProduct(exactMatch);
                    this.value = '';
                    elements.productResults.style.display = 'none';
                    showToast(`Product added via SKU: ${exactMatch.product_code}`, 'success');
                    return;
                }

                const filtered = state.products.filter(p => 
                    (p.name && p.name.toLowerCase().includes(val)) || 
                    (p.product_code && p.product_code.toString().includes(val))
                );

                elements.productResults.innerHTML = '';
                if (filtered.length === 0) {
                    elements.productResults.innerHTML = '<div class="p-3 text-center text-gray-400 text-[10px] uppercase font-black">No products found</div>';
                    elements.productResults.style.display = 'block';
                    return;
                }

                filtered.slice(0, 10).forEach(p => {
                    const item = document.createElement('div');
                    item.className = 'p-2 hover:bg-blue-50 cursor-pointer border-b border-gray-50 flex items-center gap-2 group transition-colors';
                    const img = getProductImageUrl(p);
                    item.innerHTML = `
                        <div class="w-8 h-8 rounded bg-gray-50 flex items-center justify-center overflow-hidden border border-gray-100 group-hover:border-blue-200">
                            ${img ? `<img src="${img}" class="w-full h-full object-cover">` : `<span class="text-[10px] font-black text-gray-300">${p.name.charAt(0)}</span>`}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="text-[11px] font-black text-gray-800 truncate">${escapeHTML(p.name)}</div>
                            <div class="text-[9px] font-black text-gray-400 uppercase tracking-tighter">SKU: ${escapeHTML(p.product_code || 'N/A')}</div>
                        </div>
                        <div class="text-[10px] font-black text-blue-600">₹${parseFloat(p.price || 0).toFixed(0)}</div>
                    `;
                    item.onclick = () => addProduct(p);
                    elements.productResults.appendChild(item);
                });
                elements.productResults.style.display = 'block';
            }

            function handleBarcodeInput(e) {
                if (e.key === 'Enter') {
                    const val = this.value.trim();
                    if (!val) return;
                    
                    if (!state.isCustomerSelected) {
                        showToast('Please select a customer first before scanning!', 'error');
                        return;
                    }

                    const p = state.products.find(p => p.product_code && p.product_code.toString() === val);
                    if (p) {
                        addProduct(p);
                        this.value = '';
                        showToast(`Added: ${p.name}`, 'success');
                    } else {
                        showToast(`Product with SKU "${val}" not found`, 'error');
                    }
                }
            }

            function getProductImageUrl(p) {
                if (!p.image) return null;
                return p.image.startsWith('http') ? p.image : `/storage/${p.image}`;
            }

            function populateSidebarProducts() {
                if (!elements.productGrid) return;
                elements.productGrid.innerHTML = '';
                state.products.slice(0, 20).forEach(p => {
                    const card = document.createElement('div');
                    card.className = 'bg-white p-1 rounded border border-gray-100 shadow-sm hover:border-blue-400 hover:shadow transition-all cursor-pointer group active:scale-95';
                    const img = getProductImageUrl(p);
                    card.innerHTML = `
                        <div class="w-full aspect-square bg-gray-50 rounded mb-1 flex items-center justify-center overflow-hidden border border-gray-50">
                            ${img ? `<img src="${img}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">` : `<span class="text-sm font-black text-gray-300">${p.name.charAt(0)}</span>`}
                        </div>
                        <div class="text-[9px] font-black text-gray-800 truncate leading-none mb-1">${escapeHTML(p.name)}</div>
                        <div class="text-[9px] font-black text-blue-600 leading-none">₹${parseFloat(p.price || 0).toFixed(0)}</div>
                    `;
                    card.onclick = () => addProduct(p);
                    elements.productGrid.appendChild(card);
                });
            }

            function addProduct(p) {
                if (!state.isCustomerSelected) { showToast('Select customer first', 'error'); return; }
                
                // Check if product is out of stock
                const availableStock = parseInt(p.quantity || 0);
                if (availableStock <= 0) {
                    showToast(`"${p.name}" is out of stock!`, 'error');
                    return;
                }

                elements.productResults.style.display = 'none';
                elements.productSearch.value = '';
                elements.emptyState.style.display = 'none';

                let existingRow = document.querySelector(`#itemsTable tr[data-pid="${p.id}"]`);
                if (existingRow) {
                    const qtyInput = existingRow.querySelector('.qty');
                    const currentQty = parseInt(qtyInput.value);
                    
                    if (currentQty >= availableStock) {
                        showToast(`Cannot add more. Only ${availableStock} in stock.`, 'error');
                        return;
                    }

                    qtyInput.value = currentQty + 1;
                    highlightRow(existingRow);
                } else {
                    const row = createProductRow(p);
                    elements.itemsTable.appendChild(row);
                    highlightRow(row);
                }
                calculate();
            }

            function highlightRow(row) {
                row.classList.add('bg-blue-50');
                setTimeout(() => row.classList.remove('bg-blue-50'), 500);
            }

            function createProductRow(p) {
                const tr = document.createElement('tr');
                tr.dataset.pid = p.id;
                tr.dataset.stock = p.quantity || 0;
                tr.className = 'hover:bg-gray-50 transition-colors border-b border-gray-50 animate-in fade-in slide-in-from-left-2 duration-300';
                const img = getProductImageUrl(p);
                const price = parseFloat(p.price || 0);
                const mrp = parseFloat(p.mrp || price);
                const disc = mrp > price ? ((mrp - price) / mrp * 100).toFixed(0) : 0;

                tr.innerHTML = `
                    <td class="px-2 py-1.5">
                        <div class="w-8 h-8 rounded bg-gray-50 flex items-center justify-center overflow-hidden border border-gray-100 shadow-sm">
                            ${img ? `<img src="${img}" class="w-full h-full object-cover">` : `<span class="text-[9px] font-black text-gray-300">${p.name.charAt(0)}</span>`}
                        </div>
                    </td>
                    <td class="px-2 py-1.5">
                        <div class="text-[10px] font-black text-gray-800 truncate max-w-[150px] leading-tight">${escapeHTML(p.name)}</div>
                        <div class="text-[8px] text-gray-400 font-mono tracking-tighter uppercase leading-none">${escapeHTML(p.product_code || 'N/A')}</div>
                        <input type="hidden" name="items[product_id][]" value="${p.id}">
                    </td>
                    <td class="px-2 py-1.5">
                        <input type="number" step="0.01" name="items[price][]" value="${price.toFixed(2)}" class="price-input w-full bg-blue-50/30 border border-blue-100 rounded px-1.5 py-1 text-[10px] font-black text-blue-700 focus:outline-none focus:ring-1 focus:ring-blue-500/20" oninput="InvoiceManager.calculate()">
                    </td>
                    <td class="px-2 py-1.5">
                        <input type="number" name="items[quantity][]" value="1" min="1" class="qty w-full border border-gray-100 rounded px-1.5 py-1 text-[10px] font-black text-gray-700 text-center focus:outline-none focus:ring-1 focus:ring-blue-500/20" oninput="InvoiceManager.calculate()">
                    </td>
                    <td class="px-2 py-1.5 text-right text-[10px] font-black text-gray-800 row-total">₹${price.toFixed(2)}</td>
                    <td class="px-2 py-1.5">
                        <div class="text-[8px] text-gray-400 leading-none">GST: 0.00<br>PST: 0.00</div>
                    </td>
                    <td class="px-2 py-1.5 text-center">
                        <span class="text-[8px] font-black bg-gray-100 text-gray-400 px-1 py-0.5 rounded uppercase tracking-tighter">General</span>
                    </td>
                    <td class="px-2 py-1.5 text-right">
                        <button type="button" onclick="InvoiceManager.removeProduct(this)" class="text-red-200 hover:text-red-500 transition-colors">
                            <i class="fas fa-times-circle text-[10px]"></i>
                        </button>
                    </td>
                `;
                return tr;
            }

            function removeProduct(btn) {
                const row = btn.closest('tr');
                row.classList.add('scale-95', 'opacity-0');
                setTimeout(() => {
                    row.remove();
                    if (document.querySelectorAll('#itemsTable tr[data-pid]').length === 0) {
                        elements.emptyState.style.display = 'table-row';
                    }
                    calculate();
                }, 300);
            }

            function clearAllProducts() {
                document.querySelectorAll('#itemsTable tr[data-pid]').forEach(row => row.remove());
                elements.emptyState.style.display = 'table-row';
                calculate();
            }

            function calculate() {
                let subTotal = 0;
                let count = 0;
                document.querySelectorAll('#itemsTable tr[data-pid]').forEach(row => {
                    const price = parseFloat(row.querySelector('.price-input').value) || 0;
                    const qtyInput = row.querySelector('.qty');
                    const stock = parseInt(row.dataset.stock || 0);
                    let qty = parseFloat(qtyInput.value) || 0;

                    if (qty > stock) {
                        showToast(`Only ${stock} items available in stock!`, 'error');
                        qty = stock;
                        qtyInput.value = stock;
                    }

                    const total = price * qty;
                    subTotal += total;
                    count += qty;
                    row.querySelector('.row-total').textContent = `₹${total.toFixed(2)}`;
                });

                const taxPercent = parseFloat(document.getElementById('tax').value) || 0;
                const taxAmount = (subTotal * taxPercent) / 100;
                const grandTotal = subTotal + taxAmount;

                if (elements.subTotalSpan) elements.subTotalSpan.textContent = `₹${subTotal.toFixed(2)}`;
                if (elements.totalAmountSpan) elements.totalAmountSpan.textContent = `₹${grandTotal.toFixed(2)}`;
                if (elements.itemCountSpan) elements.itemCountSpan.textContent = count;
                if (elements.totalPayableSpan) elements.totalPayableSpan.textContent = `₹${grandTotal.toFixed(2)}`;

                // Update hidden form fields
                document.getElementById('sub_total').value = subTotal.toFixed(2);
                document.getElementById('tax_amount').value = taxAmount.toFixed(2);
                document.getElementById('grand_total').value = grandTotal.toFixed(2);
            }

            function handleSubmit(event) {
                event.preventDefault();
                if (!state.isCustomerSelected) { showToast('Please select customer', 'error'); return; }
                const rows = document.querySelectorAll('#itemsTable tr[data-pid]');
                if (rows.length === 0) { showToast('Add at least one product', 'error'); return; }

                const btn = document.getElementById('finalizeBtn');
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Processing...';

                // Collect data and save via DataService
                const invoiceData = {
                    invoice_token: state.invoice_token,
                    customer: state.selectedCustomer,
                    items: Array.from(rows).map(row => ({
                        product_id: row.dataset.pid,
                        name: row.querySelector('.text-gray-800').textContent,
                        quantity: parseFloat(row.querySelector('.qty').value),
                        price: parseFloat(row.querySelector('.price-input').value)
                    })),
                    totals: {
                        subtotal: parseFloat(document.getElementById('sub_total').value),
                        tax_percent: parseFloat(document.getElementById('tax').value),
                        tax_amount: parseFloat(document.getElementById('tax_amount').value),
                        grand_total: parseFloat(document.getElementById('grand_total').value)
                    }
                };

                DataService.saveInvoice(invoiceData);
                if (navigator.onLine) {
                    DataService.syncInvoice(invoiceData.invoice_token).then(res => {
                        if (res.sale_id) {
                            showToast('Invoice finalized successfully!', 'success');
                            window.location.href = `/sales/${res.sale_id}`;
                        } else {
                            showSuccessModal(res.sale_id, invoiceData.totals.grand_total);
                        }
                    }).catch(err => showOfflineSuccess());
                } else {
                    showOfflineSuccess();
                }
            }

            function showSuccessModal(saleId, amount) {
                document.getElementById('res_invoice_no').textContent = saleId || 'SYNC-PENDING';
                document.getElementById('res_amount').textContent = `₹${parseFloat(amount).toFixed(2)}`;
                document.getElementById('successModal').classList.remove('hidden');
            }

            function showOfflineSuccess() {
                showToast('Invoice saved offline! Syncing later.', 'warning');
                setTimeout(() => window.location.href = '/sales', 2000);
            }

            // ========== UI HELPERS ==========
            function escapeHTML(str) {
                if (!str) return '';
                return String(str).replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]));
            }

            function showToast(msg, type = 'success') {
                const toast = document.createElement('div');
                toast.className = `fixed top-4 right-4 px-6 py-3 rounded-xl shadow-2xl text-white text-xs font-bold uppercase tracking-wider z-[200] animate-in slide-in-from-right duration-300 ${type === 'success' ? 'bg-green-500' : 'bg-red-500'}`;
                toast.innerHTML = `<div class="flex items-center gap-2"><i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i> ${msg}</div>`;
                document.body.appendChild(toast);
                setTimeout(() => { toast.classList.add('opacity-0'); setTimeout(() => toast.remove(), 500); }, 3000);
            }

            function openCustomerModal() { document.getElementById('customerModal').classList.remove('hidden'); }
            function closeCustomerModal() { document.getElementById('customerModal').classList.add('hidden'); }

            function saveCustomer() {
                const name = document.getElementById('c_name').value;
                const mobile = document.getElementById('c_mobile').value;
                if (!name || !mobile) { showToast('Name and mobile required', 'error'); return; }
                
                const btn = document.getElementById('saveCustomerBtn');
                btn.disabled = true;
                btn.innerHTML = 'Saving...';

                fetch("{{ route('customers.store.ajax') }}", {
                    method: "POST",
                    headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": "{{ csrf_token() }}" },
                    body: JSON.stringify({ name, mobile, email: document.getElementById('c_email').value, address: document.getElementById('c_address').value })
                }).then(res => res.json()).then(data => {
                    if (data.customer) { closeCustomerModal(); selectCustomer(data.customer); }
                    else showToast(data.message || 'Error', 'error');
                }).finally(() => { btn.disabled = false; btn.innerHTML = 'Save Customer'; });
            }

            function init() {
                if (elements.customerSearch) elements.customerSearch.addEventListener('input', handleCustomerSearch);
                if (elements.productSearch) elements.productSearch.addEventListener('input', handleProductSearch);
                if (elements.barcodeInput) elements.barcodeInput.addEventListener('keypress', handleBarcodeInput);
                document.getElementById('finalizeBtn').addEventListener('click', handleSubmit);
                document.addEventListener('click', e => {
                    if (!elements.customerSearch.contains(e.target)) elements.customerResults.style.display = 'none';
                    if (!elements.productSearch.contains(e.target)) elements.productResults.style.display = 'none';
                });
                populateSidebarProducts();
            }

            return {
                init, selectCustomer, clearCustomerSelection, addProduct, removeProduct,
                calculate, handleSubmit, openCustomerModal, closeCustomerModal, saveCustomer,
                initAddressAutocomplete, getLiveLocation
            };
        })();

        document.addEventListener('DOMContentLoaded', () => {
            InvoiceManager.init();
            InvoiceManager.initAddressAutocomplete();
        });
        window.InvoiceManager = InvoiceManager;
    </script>
@endsection
