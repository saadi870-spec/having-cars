import { Head } from '@inertiajs/react';
import { Link } from '@inertiajs/react';
import { ChevronLeft, ChevronRight, Search } from 'lucide-react';
import FleetCarCard from '@/components/fleet-car-card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { index as carsIndex } from '@/routes/cars';
import type { FleetCar, FleetFilters, Paginator } from '@/types';

type PageProps = {
    cars: Paginator<FleetCar>;
    filters: FleetFilters;
    query: Partial<{
        starts_on: string;
        ends_on: string;
        location_id: string;
        category_id: string;
        make_id: string;
        transmission_id: string;
        q: string;
    }>;
};

function datesQuery(query: PageProps['query']): string {
    return new URLSearchParams(
        Object.entries({
            starts_on: query.starts_on ?? '',
            ends_on: query.ends_on ?? '',
            location_id: query.location_id ?? '',
        }).filter(([, value]) => value !== ''),
    ).toString();
}

export default function CarsIndex({ cars, filters, query }: PageProps) {
    return (
        <>
            <Head title="Fleet" />

            <div className="space-y-6 px-4 py-6 md:px-6">
                <div>
                    <h1 className="text-2xl font-bold tracking-tight">Our fleet</h1>
                    <p className="mt-1 text-sm text-muted-foreground">
                        {cars.total} {cars.total === 1 ? 'car' : 'cars'} ready to book.
                    </p>
                </div>

                <form action={carsIndex.url()} method="get" className="grid gap-3 rounded-xl border bg-card p-4 shadow-sm md:grid-cols-3 lg:grid-cols-6">
                    <div className="grid gap-1.5">
                        <Label htmlFor="starts_on">Pickup date</Label>
                        <Input id="starts_on" name="starts_on" type="date" defaultValue={query.starts_on ?? ''} />
                    </div>
                    <div className="grid gap-1.5">
                        <Label htmlFor="ends_on">Return date</Label>
                        <Input id="ends_on" name="ends_on" type="date" defaultValue={query.ends_on ?? ''} />
                    </div>
                    <div className="grid gap-1.5">
                        <Label htmlFor="location_id">Location</Label>
                        <select
                            id="location_id"
                            name="location_id"
                            defaultValue={query.location_id ?? ''}
                            className="border-input bg-background flex h-9 w-full rounded-md border px-3 py-1 text-sm shadow-sm"
                        >
                            <option value="">Anywhere</option>
                            {filters.locations.map((location) => (
                                <option key={location.id} value={location.id}>
                                    {location.name}
                                </option>
                            ))}
                        </select>
                    </div>
                    <div className="grid gap-1.5">
                        <Label htmlFor="category_id">Category</Label>
                        <select
                            id="category_id"
                            name="category_id"
                            defaultValue={query.category_id ?? ''}
                            className="border-input bg-background flex h-9 w-full rounded-md border px-3 py-1 text-sm shadow-sm"
                        >
                            <option value="">Any</option>
                            {filters.categories.map((category) => (
                                <option key={category.id} value={category.id}>
                                    {category.name}
                                </option>
                            ))}
                        </select>
                    </div>
                    <div className="grid gap-1.5">
                        <Label htmlFor="q">Search</Label>
                        <Input id="q" name="q" placeholder="Make, model, color…" defaultValue={query.q ?? ''} />
                    </div>
                    <div className="flex items-end">
                        <Button type="submit" className="w-full">
                            <Search className="mr-1 size-4" /> Filter
                        </Button>
                    </div>
                </form>

                {cars.data.length === 0 ? (
                    <div className="rounded-xl border border-dashed p-12 text-center text-muted-foreground">
                        No cars match those filters. Try widening your search.
                    </div>
                ) : (
                    <div className="grid gap-6 sm:grid-cols-2 xl:grid-cols-3">
                        {cars.data.map((car) => (
                            <FleetCarCard key={car.id} car={car} datesQuery={datesQuery(query)} />
                        ))}
                    </div>
                )}

                {cars.last_page > 1 ? (
                    <div className="flex items-center justify-between">
                        <p className="text-sm text-muted-foreground">
                            Page {cars.current_page} of {cars.last_page}
                        </p>
                        <div className="flex gap-2">
                            {cars.prev_page_url ? (
                                <Button asChild variant="outline" size="sm">
                                    <Link href={cars.prev_page_url} preserveScroll>
                                        <ChevronLeft className="size-4" /> Previous
                                    </Link>
                                </Button>
                            ) : null}
                            {cars.next_page_url ? (
                                <Button asChild variant="outline" size="sm">
                                    <Link href={cars.next_page_url} preserveScroll>
                                        Next <ChevronRight className="size-4" />
                                    </Link>
                                </Button>
                            ) : null}
                        </div>
                    </div>
                ) : null}
            </div>
        </>
    );
}
