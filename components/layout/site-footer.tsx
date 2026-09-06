'use client'

import Link from 'next/link'
import { useState } from 'react'
import { ArrowRight } from 'lucide-react'
import { SmartImage } from '@/components/smart-image'
import { BRAND } from '@/lib/brand'

const InstagramIcon = ({ className }: { className?: string }) => (
  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" className={className}><rect width="20" height="20" x="2" y="2" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" x2="17.51" y1="6.5" y2="6.5"/></svg>
)

const COLUMNS = [
  {
    title: 'Shop',
    links: [
      { label: 'All Bouquets', href: '/shop' },
      { label: 'Under $75', href: '/shop?max=75' },
      { label: 'Subscriptions', href: '/subscriptions' },
      { label: 'Occasions', href: '/occasions' },
    ],
  },
  {
    title: 'Help',
    links: [
      { label: 'Delivery', href: '/delivery' },
      { label: 'Contact', href: '/contact' },
      { label: 'Care Tips', href: '/about' },
      { label: 'Track Order', href: '/contact' },
    ],
  },
  {
    title: 'Company',
    links: [
      { label: 'Our Story', href: '/about' },
      { label: 'The Studio', href: '/about' },
      { label: 'Careers', href: '/about' },
      { label: 'Wholesale', href: '/contact' },
    ],
  },
]

const IG = [
  '/instagram/ig-1.png',
  '/instagram/ig-2.png',
  '/instagram/ig-3.png',
  '/instagram/ig-4.png',
  '/instagram/ig-5.png',
  '/instagram/ig-6.png',
]

export function SiteFooter() {
  const [email, setEmail] = useState('')
  const [sent, setSent] = useState(false)

  return (
    <footer className="bg-card">
      {/* instagram strip */}
      <div className="grid grid-cols-3 sm:grid-cols-6">
        {IG.map((src, i) => (
          <Link
            key={i}
            href="/shop"
            className="group relative aspect-square overflow-hidden"
          >
            <SmartImage
              src={src}
              alt="Wildflower arrangement on Instagram"
              fill
              sizes="(max-width: 640px) 33vw, 16vw"
              showMotif={i % 2 === 0}
              className="object-cover transition-transform duration-500 group-hover:scale-105"
            />
            <span className="absolute inset-0 flex items-center justify-center bg-primary/0 text-primary-foreground opacity-0 transition-all duration-300 group-hover:bg-primary/30 group-hover:opacity-100">
              <InstagramIcon className="size-5" />
            </span>
          </Link>
        ))}
      </div>

      <div className="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
        {/* big serif statement */}
        <h2 className="max-w-3xl text-balance font-serif text-4xl font-medium leading-[1.05] tracking-tight sm:text-6xl">
          {BRAND.statement}
        </h2>

        <div className="mt-14 grid gap-10 md:grid-cols-2 lg:grid-cols-4">
          {COLUMNS.map((col) => (
            <div key={col.title}>
              <h3 className="font-sans text-xs uppercase tracking-[0.2em] text-muted-foreground">
                {col.title}
              </h3>
              <ul className="mt-4 space-y-2.5">
                {col.links.map((l) => (
                  <li key={l.label}>
                    <Link
                      href={l.href}
                      className="text-sm text-foreground/80 transition-colors hover:text-foreground"
                    >
                      {l.label}
                    </Link>
                  </li>
                ))}
              </ul>
            </div>
          ))}

          {/* newsletter */}
          <div>
            <h3 className="font-sans text-xs uppercase tracking-[0.2em] text-muted-foreground">
              Newsletter
            </h3>
            <p className="mt-4 text-sm text-foreground/80">
              Seasonal blooms, delivery news, the occasional secret sale.
            </p>
            <form
              onSubmit={(e) => {
                e.preventDefault()
                if (email) setSent(true)
              }}
              className="mt-4 flex items-center gap-2 border-b border-foreground/30 pb-2 focus-within:border-foreground"
            >
              <input
                type="email"
                required
                value={email}
                onChange={(e) => setEmail(e.target.value)}
                placeholder="Your email"
                className="w-full bg-transparent text-sm outline-none placeholder:text-muted-foreground"
                aria-label="Email address"
              />
              <button
                type="submit"
                aria-label="Subscribe"
                className="transition-transform hover:translate-x-0.5"
              >
                <ArrowRight className="size-4" />
              </button>
            </form>
            {sent && (
              <p className="mt-2 text-xs text-primary">
                Lovely — you&apos;re on the list.
              </p>
            )}
          </div>
        </div>

        <div className="mt-14 flex flex-col gap-4 border-t border-border pt-6 text-xs text-muted-foreground sm:flex-row sm:items-center sm:justify-between">
          <p>
            © {new Date().getFullYear()} {BRAND.name}. Made in {BRAND.city}.
          </p>
          <p>Delivering across Boston, Cambridge, Somerville, Brookline &amp; beyond.</p>
          <div className="flex items-center gap-2 text-foreground/60">
            <span className="rounded border border-border px-2 py-1">VISA</span>
            <span className="rounded border border-border px-2 py-1">MC</span>
            <span className="rounded border border-border px-2 py-1">AMEX</span>
            <span className="rounded border border-border px-2 py-1">PAY</span>
          </div>
        </div>
      </div>
    </footer>
  )
}