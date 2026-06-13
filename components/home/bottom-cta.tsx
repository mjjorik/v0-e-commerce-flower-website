import Link from 'next/link'
import { ArrowRight } from 'lucide-react'
import { KineticText } from '@/components/kinetic-text'

export function BottomCta() {
  return (
    <section className="px-4 pb-16 sm:px-6 lg:px-8">
      <div className="relative mx-auto max-w-7xl overflow-hidden rounded-3xl bg-terracotta px-6 py-20 text-center text-terracotta-foreground sm:py-28">
        <div
          aria-hidden
          className="pointer-events-none absolute -top-24 left-1/2 size-[34rem] -translate-x-1/2 rounded-full opacity-30 blur-3xl"
          style={{ background: 'radial-gradient(circle, white, transparent 70%)' }}
        />
        <p className="relative mb-5 text-[11px] font-semibold uppercase tracking-[0.3em] text-terracotta-foreground/70">
          No occasion required
        </p>
        <KineticText
          as="h2"
          text="Make someone's Tuesday."
          className="relative mx-auto max-w-3xl text-balance font-serif text-4xl leading-[1.02] tracking-tight sm:text-7xl"
        />
        <p className="relative mx-auto mt-5 max-w-md text-pretty text-terracotta-foreground/85">
          The best flowers are the ones nobody expected. Send a little joy, today.
        </p>
        <Link
          href="/shop"
          className="btn group relative mt-9 bg-background text-foreground shadow-lg hover:-translate-y-0.5"
        >
          Shop Bouquets
          <ArrowRight className="btn-arrow size-4" strokeWidth={2} />
        </Link>
      </div>
    </section>
  )
}
