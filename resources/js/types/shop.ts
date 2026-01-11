export interface MoneyDisplay {
    ugx: number;
    usd: number;
}

export interface ProductCard {
    id: number;
    name: string;
    slug: string;
    subtitle: string | null;
    category: string | null;
    price: MoneyDisplay;
    is_limited_edition: boolean;
    limited_badge_label: string | null;
    image: string | null;
    image_alt: string | null;
    stock_status: string;
}

export interface ProductDetail extends ProductCard {
    sku: string;
    description: string | null;
    materials: string | null;
    care_instructions: string | null;
    artisan_story: string | null;
    inventory: number;
    url?: string;
    image_url?: string | null;
    images: Array<{
        url: string;
        alt_text: string | null;
    }>;
    variants: Array<ProductVariant>;
}

export interface ProductVariant {
    id: number;
    name: string;
    sku: string;
    price: MoneyDisplay;
    attributes: Record<string, string | number | boolean | null> | null;
    is_default: boolean;
    inventory: number;
}

export interface CartLineItem {
    id: string;
    name: string;
    variant_name: string;
    quantity: number;
    display_price: MoneyDisplay;
    unit_price: number;
    currency: string;
    image: string | null;
    slug: string;
    sku: string;
    stock: number;
}

export interface CartTotals {
    currency: string;
    subtotal: MoneyDisplay;
    shipping: MoneyDisplay;
    grand_total: MoneyDisplay;
}

export interface CartSummary {
    items: CartLineItem[];
    totals: CartTotals;
}

export interface ShippingMethodOption {
    id: number;
    name: string;
    code: string;
    type: string;
    region: string | null;
    carrier: string | null;
    is_pickup: boolean;
    eta: string | null;
    rate: MoneyDisplay;
}

export interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

export interface PaginationMeta {
    current_page: number;
    from: number | null;
    last_page: number;
    links: PaginationLink[];
    path: string;
    per_page: number;
    to: number | null;
    total: number;
}

