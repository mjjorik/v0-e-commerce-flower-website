import Link from 'next/link'
import { ArrowRight } from 'lucide-react'
import { KineticText } from '@/components/kinetic-text'

export function BottomCta() {
  return (
    <section className="px-4 pb-16 sm:px-6 lg:px-8">
      <div className="mx-auto max-w-7xl overflow-hidden rounded-3xl bg-terracotta px-6 py-20 text-center text-terracotta-foreground sm:py-28">
        <KineticText
          as="h2"
          text="Make someone's Tuesday."
          className="mx-auto max-w-3xl text-balance font-serif text-4xl leading-[1.02] tracking-tight sm:text-7xl"
        />
        <p className="mx-auto mt-5 max-w-md text-pretty text-terracotta-foreground/85">
          No occasion required. The best flowers are the ones nobody expected.
        </p>
        <Link
          href="/shop"
          className="group mt-8 inline-flex items-center gap-2 rounded-full bg-primary px-8 py-4 text-sm text-primary-foreground transition-transform hover:-translate-y-0.5"
        >
          Shop Bouquets
          <ArrowRight className="size-4 transition-transform group-hover:translate-x-1" />
        </Link>
      </div>
    </section>
  )
}
