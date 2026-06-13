import Link from 'next/link'
import { Reveal } from '@/components/kinetic-text'
import { SmartImage } from '@/components/smart-image'
import { OCCASION_CARDS } from '@/lib/occasions'
import { cn } from '@/lib/utils'

export function OccasionsBento() {
  const [big, ...rest] = OCCASION_CARDS

  return (
    <section className="py-16 sm:py-20">
      <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div className="mb-10 flex items-end justify-between gap-4">
          <div className="max-w-lg">
            <Reveal>
              <p className="mb-2 text-xs uppercase tracking-[0.28em] text-muted-foreground">
                For every moment
              </p>
            </Reveal>
            <Reveal delay={100}>
              <h2 className="text-balance font-serif text-3xl tracking-tight sm:text-5xl">
                Flowers that say it for you
              </h2>
            </Reveal>
          </div>
          <Reveal delay={200} className="hidden sm:block">
            <Link
              href="/occasions"
              className="shrink-0 text-sm underline underline-offset-4 hover:opacity-70"
            >
              All occasions
            </Link>
          </Reveal>
        </div>

        {/* Fixed Grid Container: Added h-full and removed Reveal wrapper conflict on grid layout */}
        <div className="grid auto-rows-[200px] grid-cols-2 gap-4 sm:auto-rows-[260px] lg:grid-cols-4">
          {/* large feature */}
          <Reveal className="col-span-2 row-span-2 h-full">
            <OccasionTile
              occasion={big}
              className="h-full"
              big
            />
          </Reveal>
          {rest.map((o, i) => (
            <Reveal key={o.slug} delay={i * 100} className="h-full">
              <OccasionTile occasion={o} className="h-full" />
            </Reveal>
          ))}
        </div>
      </div>
    </section>
  )
}

function OccasionTile({
  occasion,
  className = '',
  big = false,
}: {
  occasion: (typeof OCCASION_CARDS)[number]
  className?: string
  big?: boolean
}) {
  return (
    <Link
      href={`/occasions#${occasion.slug}`}
      className={cn(
        "group relative flex flex-col overflow-hidden rounded-2xl bg-card",
        className
      )}
    >
      <SmartImage
        src={occasion.image}
        alt={occasion.imageAlt}
        fill
        sizes={big ? '(max-width: 1024px) 100vw, 50vw' : '(max-width: 1024px) 50vw, 25vw'}
        showMotif={false}
        className="object-cover transition-transform duration-700 group-hover:scale-105"
      />
      <div className="absolute inset-0 bg-gradient-to-t from-black/60 via-black/10 to-transparent" />
      <div className="absolute bottom-0 left-0 p-4 sm:p-5 w-full">
        <h3
          className={cn(
            "font-serif text-white leading-tight",
            big ? 'text-3xl sm:text-4xl' : 'text-xl sm:text-2xl'
          )}
        >
          {occasion.title}
        </h3>
        <p className="mt-1 text-sm text-white/80">{occasion.blurb}</p>
      </div>
    </Link>
  )
}
