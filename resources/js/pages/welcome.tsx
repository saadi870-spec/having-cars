import { Form, Head } from '@inertiajs/react';
import { MapPin } from 'lucide-react';
import FleetCarCard from '@/components/fleet-car-card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { home } from '@/routes';
import type { FleetCar, FleetFilters } from '@/types';

type PageProps = {
    featured: FleetCar[];
    filters: FleetFilters;
};

export default function Welcome({ featured, filters }: PageProps) {
    return (
        <>
            <Head title="Home" />

            <div className="flex min-h-screen flex-col bg-background text-foreground">
                <header className="sticky top-0 z-40 border-b bg-background/80 backdrop-blur">
                    <div className="mx-auto flex h-16 max-w-6xl items-center justify-between px-4">
                        <a href={home()} className="text-lg font-bold tracking-tight">
                            Apex<span className="text-primary">Drive</span>
                        </a>
                        <nav className="flex items-center gap-4 text-sm">
                            <a href="/cars" className="text-muted-foreground transition-colors hover:text-foreground">
                                Fleet
                            </a>
                            <a href="/login" className="transition-colors hover:text-foreground">
                                Log in
                            </a>
                            <Button asChild>
                                <a href="/register">Register</a>
                            </Button>
                        </nav>
                    </div>
                </header>

                <main className="flex-1">
                    <section className="mx-auto max-w-6xl px-4 py-16 text-center md:py-24">
                        <h1 className="mx-auto max-w-3xl text-4xl font-extrabold tracking-tight md:text-6xl">
                            Rent a car for the{' '}
                            <span className="text-primary">way you actually drive</span>
                        </h1>
                        <p className="mx-auto mt-6 max-w-2xl text-lg text-muted-foreground">
                            Airport-ready sedans, family SUVs, and weekend sports cars across the Bay Area.
                            Pick your dates and we will hold the keys.
                        </p>

                        <Form action="/cars" method="get" className="mx-auto mt-10 max-w-4xl">
                            {({ errors }) => (
                                <div className="grid gap-3 rounded-xl border bg-card p-4 shadow-sm md:grid-cols-[1fr_1fr_1fr_auto]">
                                    <div className="grid gap-1.5 text-left">
                                        <Label htmlFor="pickup_location_id">Pickup location</Label>
                                        <select
                                            id="pickup_location_id"
                                            name="location_id"
                                            defaultValue=""
                                            className="border-input bg-background flex h-9 w-full rounded-md border px-3 py-1 text-sm shadow-sm"
                                        >
                                            <option value="">Anywhere</option>
                                            {filters.locations.map((location) => (
                                                <option key={location.id} value={location.id}>
                                                    {location.name} · {location.city}
                                                </option>
                                            ))}
                                        </select>
                                    </div>
                                    <div className="grid gap-1.5 text-left">
                                        <Label htmlFor="starts_on">Pickup date</Label>
                                        <Input id="starts_on" name="starts_on" type="date" />
                                        {errors.starts_on ? (
                                            <p className="text-sm text-destructive">{errors.starts_on}</p>
                                        ) : null}
                                    </div>
                                    <div className="grid gap-1.5 text-left">
                                        <Label htmlFor="ends_on">Return date</Label>
                                        <Input id="ends_on" name="ends_on" type="date" />
                                        {errors.ends_on ? (
                                            <p className="text-sm text-destructive">{errors.ends_on}</p>
                                        ) : null}
                                    </div>
                                    <div className="flex items-end">
                                        <Button type="submit" className="w-full md:w-auto">
                                            <MapPin className="mr-1 size-4" /> Search cars
                                        </Button>
                                    </div>
                                </div>
                            )}
                        </Form>
                    </section>

                    <section className="mx-auto max-w-6xl px-4 pb-20">
                        <div className="flex items-end justify-between">
                            <div>
                                <h2 className="text-2xl font-bold tracking-tight">Featured cars</h2>
                                <p className="mt-1 text-sm text-muted-foreground">
                                    A hand-picked slice of the fleet.
                                </p>
                            </div>
                            <a href="/cars" className="text-sm font-medium text-primary hover:underline">
                                View all cars →
                            </a>
                        </div>

                        <div className="mt-6 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                            {featured.map((car) => (
                                <FleetCarCard key={car.id} car={car} />
                            ))}
                        </div>
                    </section>
                </main>

                <footer className="border-t py-8 text-center text-sm text-muted-foreground">
                    © {new Date().getFullYear()} ApexDrive · Drive easy.
                </footer>
            </div>
        </>
    );
}
