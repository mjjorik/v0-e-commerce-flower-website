import Link from 'next/link'
import Image from 'next/image'
import { ArrowRight } from 'lucide-react'
import { Reveal } from '@/components/kinetic-text'

export function SubscriptionTeaser() {
  return (
    <section className="py-16 sm:py-20">
      <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div className="grid items-center gap-6 overflow-hidden rounded-3xl bg-sage/40 lg:grid-cols-2">
          <div className="relative aspect-[4/3] lg:aspect-auto lg:h-full lg:min-h-[28rem]">
            <Image
              src="/subscriptions/teaser.png"
              alt="A weekly flower subscription bouquet on a kitchen counter"
              fill
              sizes="(max-width: 1024px) 100vw, 50vw"
              className="object-cover"
            />
          </div>
          <div className="px-6 py-10 sm:px-10 lg:py-16">
            <Reveal>
              <p className="mb-3 text-xs uppercase tracking-[0.28em] text-primary/70">
                The ritual
              </p>
              <h2 className="text-balance font-serif text-4xl leading-[1.02] tracking-tight sm:text-5xl">
                Fresh flowers, every week
              </h2>
              <p className="mt-5 max-w-md text-pretty leading-relaxed text-foreground/75">
                A standing order of seasonal blooms, chosen by our studio and
                delivered like clockwork. Pause, skip or cancel anytime — no
                strings, just stems.
              </p>
              <p className="mt-6 font-serif text-2xl">
                From <span className="text-terracotta">$55</span>
                <span className="text-base text-foreground/60"> / delivery</span>
              </p>
              <Link
                href="/subscriptions"
                className="group mt-7 inline-flex items-center gap-2 rounded-full bg-primary px-7 py-4 text-sm text-primary-foreground transition-transform hover:-translate-y-0.5"
              >
                Explore subscriptions
                <ArrowRight className="size-4 transition-transform group-hover:translate-x-1" />
              </Link>
            </Reveal>
          </div>
        </div>
      </div>
    </section>
  )
}
