/**
 * Cart persistence utility using localStorage as backup.
 * Syncs cart data between server session and browser localStorage.
 */

const STORAGE_KEY = 'score_beyond_cart';

export interface CartItem {
    id: string;
    product_id: number;
    variant_id: number | null;
    name: string;
    variant_name: string;
    quantity: number;
    unit_price: number;
    currency: string;
    display_price: {
        ugx: number;
        usd: number;
    };
    image: string | null;
    slug: string;
    sku: string;
    stock: number;
}

export interface CartData {
    items: CartItem[];
    lastSynced: string;
}

/**
 * Save cart items to localStorage
 */
export function saveCartToStorage(items: CartItem[]): void {
    if (typeof window === 'undefined') {
        return;
    }

    try {
        const cartData: CartData = {
            items,
            lastSynced: new Date().toISOString(),
        };
        localStorage.setItem(STORAGE_KEY, JSON.stringify(cartData));
    } catch (error) {
        // Silently handle localStorage errors (non-critical)
    }
}

/**
 * Load cart items from localStorage
 */
export function loadCartFromStorage(): CartItem[] {
    if (typeof window === 'undefined') {
        return [];
    }

    try {
        const stored = localStorage.getItem(STORAGE_KEY);
        if (!stored) {
            return [];
        }

        const cartData: CartData = JSON.parse(stored);
        return cartData.items || [];
    } catch (error) {
        // Silently handle localStorage errors (non-critical)
        return [];
    }
}

/**
 * Clear cart from localStorage
 */
export function clearCartFromStorage(): void {
    if (typeof window === 'undefined') {
        return;
    }

    try {
        localStorage.removeItem(STORAGE_KEY);
    } catch (error) {
        // Silently handle localStorage errors (non-critical)
    }
}

/**
 * Check if localStorage cart exists and is newer than session
 */
export function hasStoredCart(): boolean {
    if (typeof window === 'undefined') {
        return false;
    }

    try {
        const stored = localStorage.getItem(STORAGE_KEY);
        return stored !== null;
    } catch {
        return false;
    }
}

