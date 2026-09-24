export type FleetLocation = {
    id: number;
    name: string;
    city: string;
};

export type FleetQuote = {
    days: number;
    total: string;
};

export type FleetCar = {
    id: number;
    name: string;
    year: number;
    color: string;
    seats: number;
    doors: number;
    daily_rate: string;
    image_url: string | null;
    description: string | null;
    license_plate: string;
    make: string;
    model: string;
    category: string;
    fuel: string;
    transmission: string;
    status: string;
    status_code: string;
    location: FleetLocation;
    quote: FleetQuote | null;
    car_model_id?: number;
    car_category_id?: number;
    fuel_type_id?: number;
    transmission_type_id?: number;
    car_status_id?: number;
    location_id?: number;
};

export type RentalSummary = {
    id: number;
    starts_on: string;
    ends_on: string;
    days: number;
    daily_rate: string;
    total_amount: string;
    notes: string | null;
    status: string;
    status_code: string;
    cancellable: boolean;
    car: {
        id: number;
        name: string;
        image_url: string | null;
        license_plate: string;
    };
    pickup: FleetLocation;
    dropoff: FleetLocation;
    customer: {
        id: number;
        name: string;
        email: string;
    };
    rental_status_id?: number;
};

export type FleetFilters = {
    locations: FleetLocation[];
    categories: { id: number; name: string; code: string }[];
    makes: { id: number; name: string }[];
    transmissions: { id: number; name: string }[];
};

export type AdminLookups = {
    makes: { id: number; name: string }[];
    models: { id: number; name: string; car_make_id: number }[];
    categories: { id: number; name: string }[];
    fuelTypes: { id: number; name: string }[];
    transmissions: { id: number; name: string }[];
    statuses: { id: number; name: string; code: string }[];
    locations: FleetLocation[];
    rentalStatuses: { id: number; name: string; code: string }[];
};

export type Paginator<T> = {
    data: T[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    links: { url: string | null; label: string; active: boolean }[];
    prev_page_url: string | null;
    next_page_url: string | null;
};

export type CatalogItem = {
    id: number;
    is_active: boolean;
    sort_order: number;
    code: string;
    name: string;
    description?: string;
    car_make_id?: number;
    make_name?: string;
    address?: string;
    city?: string;
    phone?: string;
};
