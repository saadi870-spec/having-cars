import { Link } from '@inertiajs/react';
import { CalendarDays, Fuel, Gauge, MapPin, Users } from 'lucide-react';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { cn } from '@/lib/utils';
import type { FleetCar } from '@/types';

export function statusVariant(code: string): 'default' | 'secondary' | 'destructive' | 'outline' {
    if (code === 'available') return 'default';
    if (code === 'maintenance') return 'secondary';
    if (code === 'retired') return 'destructive';

    return 'outline';
}

export default function FleetCarCard({ car, datesQuery = '' }: { car: FleetCar; datesQuery?: string }) {
    const showUrl = `/cars/${car.id}${datesQuery ? `?${datesQuery}` : ''}`;

    return (
        <div className="group flex flex-col overflow-hidden rounded-xl border bg-card text-card-foreground shadow-sm transition-shadow hover:shadow-md">
            <Link href={showUrl} className="relative block aspect-[16/10] overflow-hidden bg-muted">
                {car.image_url ? (
                    <img
                        src={car.image_url}
                        alt={`${car.make} ${car.model}`}
                        className="size-full object-cover transition-transform duration-300 group-hover:scale-105"
                        loading="lazy"
                    />
                ) : (
                    <div className="flex size-full items-center justify-center text-4xl">🚗</div>
                )}
                <Badge variant={statusVariant(car.status_code)} className="absolute top-3 left-3">
                    {car.status}
                </Badge>
            </Link>

            <div className="flex flex-1 flex-col gap-3 p-4">
                <div>
                    <div className="flex items-start justify-between gap-2">
                        <h3 className="font-semibold">
                            <Link href={showUrl}>
                                {car.year} {car.name}
                            </Link>
                        </h3>
                        <div className="text-right">
                            <div className="text-lg font-bold">${car.daily_rate}</div>
                            <div className="text-xs text-muted-foreground">per day</div>
                        </div>
                    </div>
                    <p className="mt-1 flex items-center gap-1 text-sm text-muted-foreground">
                        <MapPin className="size-3.5" /> {car.location.name}
                    </p>
                </div>

                {car.description ? (
                    <p className="line-clamp-2 text-sm text-muted-foreground">{car.description}</p>
                ) : null}

                <div className="mt-auto flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-muted-foreground">
                    <span className="flex items-center gap-1">
                        <Users className="size-3.5" /> {car.seats} seats
                    </span>
                    <span className="flex items-center gap-1">
                        <Gauge className="size-3.5" /> {car.transmission}
                    </span>
                    <span className="flex items-center gap-1">
                        <Fuel className="size-3.5" /> {car.fuel}
                    </span>
                </div>

                {car.quote ? (
                    <div className="flex items-center gap-1.5 rounded-md bg-muted px-3 py-2 text-sm">
                        <CalendarDays className="size-4 text-muted-foreground" />
                        <span>
                            {car.quote.days} {car.quote.days === 1 ? 'day' : 'days'} ·{' '}
                            <span className="font-semibold">${car.quote.total}</span> total
                        </span>
                    </div>
                ) : null}

                <Button asChild className={cn('w-full')}>
                    <Link href={showUrl}>View details</Link>
                </Button>
            </div>
        </div>
    );
}
