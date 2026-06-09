'use client'

import Link from 'next/link'
import Image from 'next/image'
import { Reveal } from '@/components/kinetic-text'

const CATEGORIES = [
  { label: 'Bestsellers', href: '/shop?sort=featured', image: '/categories/bestsellers.png' },
  { label: 'Under $75', href: '/shop?max=75', image: '/categories/under-75.png' },
  { label: 'Seasonal', href: '/shop?seasonal=1', image: '/categories/seasonal.png' },
  { label: 'Subscriptions', href: '/subscriptions', image: '/categories/subscriptions.png' },
  { label: 'Occasions', href: '/occasions', image: '/categories/occasions.png' },
]

export function CategoryRow() {
  return (
    <section className="py-16 sm:py-20">
      <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <Reveal className="mb-8 flex items-end justify-between gap-4">
          <h2 className="font-serif text-3xl tracking-tight sm:text-4xl">
            Find your bunch
          </h2>
          <Link
            href="/shop"
            className="shrink-0 text-sm underline underline-offset-4 hover:opacity-70"
          >
            View all
          </Link>
        </Reveal>
      </div>

      <div className="no-scrollbar flex gap-4 overflow-x-auto px-4 pb-2 sm:px-6 lg:px-8">
        {CATEGORIES.map((cat) => (
          <Link
            key={cat.label}
            href={cat.href}
            className="group relative w-[68vw] shrink-0 overflow-hidden rounded-2xl sm:w-72"
          >
            <div className="relative aspect-[3/4]">
              <Image
                src={cat.image || '/placeholder.svg'}
                alt={cat.label}
                fill
                sizes="(max-width: 640px) 68vw, 18rem"
                className="object-cover transition-transform duration-700 group-hover:scale-105"
              />
              <div className="absolute inset-0 bg-gradient-to-t from-primary/50 to-transparent" />
              <h3 className="absolute bottom-4 left-4 font-serif text-2xl text-primary-foreground">
                {cat.label}
              </h3>
            </div>
          </Link>
        ))}
      </div>
    </section>
  )
}
