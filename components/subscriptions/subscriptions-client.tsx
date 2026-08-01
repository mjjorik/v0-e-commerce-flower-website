'use client'

import { useState } from 'react'
import { Check, ArrowRight, CalendarDays, Sparkles, PauseCircle } from 'lucide-react'
import { useCart } from '@/components/cart/cart-provider'
import { formatPrice } from '@/lib/products'
import { cn } from '@/lib/utils'
import { Reveal } from '@/components/kinetic-text'

const PLANS = [
  { id: 'weekly', title: 'Weekly', price: 55, cadence: 'Every Tuesday', desc: 'Fresh blooms every week. The ultimate reset.', popular: false },
  { id: 'biweekly', title: 'Bi-Weekly', price: 65, cadence: 'Every other Tuesday', desc: 'A beautiful rhythm without the rush.', popular: true },
  { id: 'monthly', title: 'Monthly', price: 75, cadence: 'First Tuesday monthly', desc: 'A standing monthly treat for the home.', popular: false },
]

const INCLUDED = [
  'Skip, pause or cancel anytime',
  'Free delivery on every drop',
  'Exclusive seasonal stems',
  'Florist’s-choice, always in season',
]

const STEPS = [
  { icon: CalendarDays, title: 'Choose a rhythm', body: 'Weekly, bi-weekly or monthly — whatever keeps your space alive.' },
  { icon: Sparkles, title: 'We design it fresh', body: 'Our florists hand-tie a seasonal bouquet from New England farms.' },
  { icon: PauseCircle, title: 'Stay in control', body: 'Pause for a trip, skip a week or cancel — no calls, no penalties.' },
]

export function SubscriptionsClient() {
  const [selected, setSelected] = useState(PLANS[1].id)
  const { addItem } = useCart()
  const activePlan = PLANS.find((p) => p.id === selected) ?? PLANS[0]

  const handleSubscribe = () => {
    addItem({
      productSlug: `sub-${activePlan.id}`,
      name: `${activePlan.title} Subscription`,
      sizeKey: activePlan.id,
      sizeLabel: activePlan.title,
      price: activePlan.price,
      image: '/subscriptions/teaser.png',
      imageAlt: 'Wildflower subscription bouquet',
    })
  }

  return (
    <div className="min-h-[100dvh] w-full bg-background pb-32">
      {/* Hero */}
      <section className="mx-auto max-w-[90rem] px-4 pt-28 text-center sm:px-6 md:pt-36 lg:px-8">
        <div className="mx-auto max-w-4xl">
          <Reveal>
            <div className="mx-auto mb-8 inline-flex items-center gap-2 rounded-full border border-ink/10 px-4 py-1.5 text-[10px] font-semibold uppercase tracking-[0.3em] text-ink/45">
              <span className="size-1.5 rounded-full bg-terracotta" />
              The ritual
            </div>
          </Reveal>
          <Reveal delay={100}>
            <h1 className="font-serif text-5xl font-medium leading-[0.95] tracking-tighter sm:text-6xl md:text-7xl lg:text-8xl">
              Fresh flowers, <br />
              <span className="italic text-terracotta">on repeat.</span>
            </h1>
          </Reveal>
          <Reveal delay={200}>
            <p className="mx-auto mt-8 max-w-xl text-pretty text-lg leading-snug text-foreground/55">
              A standing order of seasonal blooms, chosen by our studio and
              delivered like clockwork across Greater Boston. No strings — just stems.
            </p>
          </Reveal>
        </div>
      </section>

      {/* Plan cards */}
      <section className="mx-auto mt-20 max-w-[80rem] px-4 sm:px-6 lg:px-8">
        <div className="grid items-stretch gap-6 md:grid-cols-3">
          {PLANS.map((plan, i) => {
            const isSelected = selected === plan.id
            return (
              <Reveal key={plan.id} delay={100 + i * 100} className="h-full">
                <button
                  onClick={() => setSelected(plan.id)}
                  aria-pressed={isSelected}
                  className={cn(
                    'group relative flex h-full w-full flex-col overflow-hidden rounded-2xl border p-8 text-left transition-all duration-500 ease-[var(--ease-luxe)] sm:p-10',
                    isSelected
                      ? 'border-terracotta/40 bg-card shadow-[0_24px_60px_-30px_rgba(20,36,22,0.4)] ring-1 ring-terracotta/30'
                      : 'border-border bg-transparent hover:-translate-y-1 hover:bg-card/60',
                  )}
                >
                  <div className="flex items-center justify-between">
                    <span
                      className={cn(
                        'inline-flex items-center rounded-full px-3 py-1 text-[10px] font-semibold uppercase tracking-widest transition-colors',
                        plan.popular
                          ? 'bg-terracotta text-terracotta-foreground'
                          : 'border border-border text-muted-foreground',
                      )}
                    >
                      {plan.popular ? 'Most popular' : plan.cadence}
                    </span>
                    <span
                      className={cn(
                        'flex size-6 items-center justify-center rounded-full border transition-all',
                        isSelected
                          ? 'border-terracotta bg-terracotta text-terracotta-foreground'
                          : 'border-border text-transparent',
                      )}
                    >
                      <Check className="size-3.5" strokeWidth={2.5} />
                    </span>
                  </div>

                  <h3 className="mt-6 font-serif text-3xl">{plan.title}</h3>
                  <div className="mt-3 flex items-baseline gap-1">
                    <span className="font-serif text-4xl text-terracotta">
                      {formatPrice(plan.price)}
                    </span>
                    <span className="text-sm text-muted-foreground">/delivery</span>
                  </div>
                  <p className="mt-5 leading-relaxed text-foreground/60">{plan.desc}</p>

                  <ul className="mt-auto space-y-3 pt-10 text-sm text-foreground/70">
                    {INCLUDED.map((item) => (
                      <li key={item} className="flex items-center gap-3">
                        <Check className="size-4 shrink-0 text-terracotta" strokeWidth={1.75} />
                        {item}
                      </li>
                    ))}
                  </ul>
                </button>
              </Reveal>
            )
          })}
        </div>

        {/* CTA */}
        <div className="mt-16 flex flex-col items-center gap-4">
          <Reveal delay={300}>
            <button onClick={handleSubscribe} className="btn-primary btn-lg group">
              Start {activePlan.title.toLowerCase()} · {formatPrice(activePlan.price)}/delivery
              <ArrowRight className="btn-arrow size-4" strokeWidth={2} />
            </button>
          </Reveal>
          <p className="text-sm text-muted-foreground">
            No commitment — pause, skip or cancel whenever you like.
          </p>
        </div>
      </section>

      {/* How it works */}
      <section className="mx-auto mt-28 max-w-[80rem] px-4 sm:px-6 lg:px-8">
        <Reveal>
          <h2 className="max-w-md text-balance font-serif text-3xl tracking-tight sm:text-4xl">
            How your subscription works
          </h2>
        </Reveal>
        <div className="mt-12 grid gap-10 sm:grid-cols-3 sm:gap-8">
          {STEPS.map((step, i) => (
            <Reveal key={step.title} delay={i * 120}>
              <div className="flex flex-col gap-4">
                <span className="flex size-12 items-center justify-center rounded-xl bg-sage/50 text-primary">
                  <step.icon className="size-5" strokeWidth={1.6} />
                </span>
                <h3 className="font-serif text-2xl tracking-tight">{step.title}</h3>
                <p className="text-pretty leading-relaxed text-foreground/65">{step.body}</p>
              </div>
            </Reveal>
          ))}
        </div>
      </section>
    </div>
  )
}
