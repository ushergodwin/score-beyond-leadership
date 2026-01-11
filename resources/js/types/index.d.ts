export interface User {
    id: number;
    name: string;
    email: string;
    email_verified_at?: string;
}

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

export type PageProps<
    T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
    auth: {
        user: User | null;
    };
    appName: string;
    cart: {
        count: number;
        items: CartItem[];
    };
    flash?: {
        success?: string;
        error?: string;
        message?: string;
        cart_message?: string;
        info?: string;
    };
    processing?: boolean;
    unreadNotificationsCount?: number;
};
