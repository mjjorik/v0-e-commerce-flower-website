import Link from 'next/link'
import Image from 'next/image'
import { Reveal } from '@/components/kinetic-text'
import { OCCASION_CARDS } from '@/lib/occasions'

export function OccasionsBento() {
  const [big, ...rest] = OCCASION_CARDS

  return (
    <section className="py-16 sm:py-20">
      <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <Reveal className="mb-10 flex items-end justify-between gap-4">
          <div className="max-w-lg">
            <p className="mb-2 text-xs uppercase tracking-[0.28em] text-muted-foreground">
              For every moment
            </p>
            <h2 className="text-balance font-serif text-3xl tracking-tight sm:text-5xl">
              Flowers that say it for you
            </h2>
          </div>
          <Link
            href="/occasions"
            className="hidden shrink-0 text-sm underline underline-offset-4 hover:opacity-70 sm:block"
          >
            All occasions
          </Link>
        </Reveal>

        <div className="grid auto-rows-[200px] grid-cols-2 gap-4 sm:auto-rows-[260px] lg:grid-cols-4">
          {/* large feature */}
          <OccasionTile
            occasion={big}
            className="col-span-2 row-span-2"
            big
          />
          {rest.map((o) => (
            <OccasionTile key={o.slug} occasion={o} />
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
      className={`group relative overflow-hidden rounded-2xl ${className}`}
    >
      <Image
        src={occasion.image || '/placeholder.svg'}
        alt={occasion.imageAlt}
        fill
        sizes={big ? '(max-width: 1024px) 100vw, 50vw' : '(max-width: 1024px) 50vw, 25vw'}
        className="object-cover transition-transform duration-700 group-hover:scale-105"
      />
      <div className="absolute inset-0 bg-gradient-to-t from-primary/55 via-primary/10 to-transparent" />
      <div className="absolute bottom-0 left-0 p-4 sm:p-5">
        <h3
          className={`font-serif text-primary-foreground ${big ? 'text-3xl sm:text-4xl' : 'text-xl sm:text-2xl'}`}
        >
          {occasion.title}
        </h3>
        <p className="text-sm text-primary-foreground/80">{occasion.blurb}</p>
      </div>
    </Link>
  )
}
