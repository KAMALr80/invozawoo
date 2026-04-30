/**
 * DataService - The Local Data Engine for Offline POS
 * Handles localStorage persistence, search, and server synchronization.
 */
const DataService = (function() {
    const STORAGE_KEYS = {
        INVOICES: 'pos_offline_invoices',
        PRODUCTS: 'pos_local_products',
        CUSTOMERS: 'pos_local_customers'
    };

    // Helper: Get from localStorage
    function get(key) {
        const data = localStorage.getItem(key);
        return data ? JSON.parse(data) : null;
    }

    // Helper: Save to localStorage
    function save(key, data) {
        localStorage.setItem(key, JSON.stringify(data));
    }

    return {
        /**
         * Get all invoices from local storage
         */
        getAllInvoices: function() {
            return get(STORAGE_KEYS.INVOICES) || {};
        },

        /**
         * Initial data bootstrap from server-rendered Blade data
         */
        bootstrap: function(products, customers) {
            // Only overwrite local storage if the server actually provides data.
            // This prevents an offline server from clearing the local cache.
            if (products && Array.isArray(products) && products.length > 0) {
                save(STORAGE_KEYS.PRODUCTS, products);
            }
            if (customers && Array.isArray(customers) && customers.length > 0) {
                save(STORAGE_KEYS.CUSTOMERS, customers);
            }
        },

        /**
         * Get local products for search
         */
        getProducts: function() {
            return get(STORAGE_KEYS.PRODUCTS) || [];
        },

        /**
         * Get local customers for search
         */
        getCustomers: function() {
            return get(STORAGE_KEYS.CUSTOMERS) || [];
        },

        /**
         * Save a new invoice locally
         */
        saveInvoice: function(invoiceData) {
            const invoices = get(STORAGE_KEYS.INVOICES) || {};
            // Use invoice_token as key for easy lookup
            invoices[invoiceData.invoice_token] = {
                ...invoiceData,
                synced: false,
                created_at: new Date().toISOString()
            };
            save(STORAGE_KEYS.INVOICES, invoices);
            return invoiceData.invoice_token;
        },

        /**
         * Retrieve a specific invoice by token
         */
        getInvoice: function(token) {
            const invoices = get(STORAGE_KEYS.INVOICES) || {};
            return invoices[token] || null;
        },

        /**
         * Mark an invoice as successfully synced
         */
        markSynced: function(token) {
            const invoices = get(STORAGE_KEYS.INVOICES) || {};
            if (invoices[token]) {
                invoices[token].synced = true;
                save(STORAGE_KEYS.INVOICES, invoices);
            }
        },

        /**
         * Sync an invoice to the server
         */
        syncInvoice: async function(token) {
            if (!navigator.onLine) {
                throw new Error('OFFLINE');
            }

            const invoice = this.getInvoice(token);
            if (!invoice) {
                throw new Error('Invoice not found locally');
            }

            try {
                const response = await fetch('/sync-invoice', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    },
                    body: JSON.stringify(invoice)
                });

                // Read response as text first in case it's not JSON
                const responseText = await response.text();
                let result;
                try {
                    result = JSON.parse(responseText);
                } catch (e) {
                    result = { message: 'Server Error' };
                }

                if (!response.ok) {
                    // Check for DB connection errors specifically
                    if (result.error_type === 'DATABASE_ERROR') {
                        throw new Error('DATABASE_ERROR');
                    }
                    throw new Error(result.message || 'Sync failed');
                }

                this.markSynced(token);
                return result;

            } catch (error) {
                console.error('Sync Error:', error);
                throw error;
            }
        },
        
        /**
         * Download latest data from server
         */
        downloadData: async function() {
            if (!navigator.onLine) {
                throw new Error('OFFLINE');
            }

            try {
                const response = await fetch('/pos/sync-data');
                if (!response.ok) throw new Error('Failed to fetch data');
                
                const result = await response.json();
                if (result.success) {
                    this.bootstrap(result.products, result.customers);
                    return result;
                } else {
                    throw new Error(result.message || 'Download failed');
                }
            } catch (error) {
                console.error('Download Error:', error);
                throw error;
            }
        }
    };
})();
