'use client'

import { useState } from 'react'
import { Check, ArrowRight } from 'lucide-react'
import { useCart } from '@/components/cart/cart-provider'
import { formatPrice } from '@/lib/products'
import { cn } from '@/lib/utils'
import { Reveal } from '@/components/kinetic-text'

const PLANS = [
  { id: 'weekly', title: 'Weekly', price: 55, desc: 'Fresh blooms every Tuesday. The ultimate reset.' },
  { id: 'biweekly', title: 'Bi-Weekly', price: 65, desc: 'Every other Tuesday. A beautiful rhythm.' },
  { id: 'monthly', title: 'Monthly', price: 75, desc: 'The first Tuesday of the month. A monthly treat.' }
]

export function SubscriptionsClient() {
  const [selected, setSelected] = useState(PLANS[1].id)
  const { addItem } = useCart()

  const handleSubscribe = () => {
    const plan = PLANS.find(p => p.id === selected)
    if (!plan) return
    addItem({
      productSlug: `sub-${plan.id}`,
      name: `${plan.title} Subscription`,
      sizeKey: plan.id,
      sizeLabel: plan.title,
      price: plan.price,
      image: '/products/sunday-market.png', // placeholder
      imageAlt: 'Subscription bouquet',
    })
  }

  return (
    <div className="min-h-[100dvh] w-full bg-[#FAF7F2] pb-32">
      
      {/* Hero Section */}
      <section className="mx-auto max-w-[90rem] px-4 pt-24 sm:px-6 md:pt-32 lg:px-8 text-center">
        <div className="mx-auto max-w-4xl">
          <Reveal>
            <div className="mx-auto mb-8 inline-flex items-center rounded-full border border-black/10 px-4 py-1.5 text-[10px] font-medium tracking-[0.2em] uppercase text-black/60">
              The Ritual
            </div>
          </Reveal>
          <Reveal delay={100}>
            <h1 className="font-serif text-5xl leading-[1.05] tracking-tight sm:text-6xl md:text-7xl lg:text-8xl">
              Fresh flowers, <br/>
              <span className="italic text-black/40">on repeat.</span>
            </h1>
          </Reveal>
        </div>
      </section>

      {/* Cards Section: Soft Structuralism */}
      <section className="mx-auto mt-20 max-w-[80rem] px-4 sm:px-6 lg:px-8">
        <div className="grid gap-6 md:grid-cols-3">
          {PLANS.map((plan, i) => {
            const isSelected = selected === plan.id
            return (
              <Reveal key={plan.id} delay={100 + i * 100}>
                <button
                  onClick={() => setSelected(plan.id)}
                  className={cn(
                    "group relative w-full flex flex-col text-left rounded-[2.5rem] p-1.5 transition-all duration-700 ease-[cubic-bezier(0.32,0.72,0,1)]",
                    isSelected ? "bg-black/5 ring-1 ring-black/5" : "bg-transparent hover:bg-black/5"
                  )}
                >
                  <div className={cn(
                    "relative flex h-full w-full flex-col overflow-hidden rounded-[calc(2.5rem-0.375rem)] p-8 transition-colors duration-700 sm:p-10",
                    isSelected ? "bg-white shadow-[0_8px_30px_rgb(0,0,0,0.04)]" : "bg-transparent"
                  )}>
                    {isSelected && (
                      <div className="absolute top-8 right-8 rounded-full bg-[#2D4A32] px-3 py-1 text-[10px] uppercase tracking-widest text-white">
                        Selected
                      </div>
                    )}
                    
                    <h3 className="font-serif text-3xl">{plan.title}</h3>
                    <div className="mt-4 flex items-baseline gap-1">
                      <span className="font-serif text-4xl text-[#C77B5A]">{formatPrice(plan.price)}</span>
                      <span className="text-sm text-black/40">/delivery</span>
                    </div>
                    
                    <p className="mt-6 text-black/60 leading-relaxed">{plan.desc}</p>
                    
                    <div className="mt-auto pt-10">
                      <div className="h-px w-full bg-black/5 mb-6" />
                      <ul className="space-y-4 text-sm text-black/70">
                        <li className="flex items-center gap-3"><Check className="size-4 text-[#2D4A32]" strokeWidth={1.5} /> Skip or cancel anytime</li>
                        <li className="flex items-center gap-3"><Check className="size-4 text-[#2D4A32]" strokeWidth={1.5} /> Free delivery</li>
                        <li className="flex items-center gap-3"><Check className="size-4 text-[#2D4A32]" strokeWidth={1.5} /> Exclusive seasonal stems</li>
                      </ul>
                    </div>
                  </div>
                </button>
              </Reveal>
            )
          })}
        </div>

        {/* Nested CTA Architecture (Button-in-Button) */}
        <div className="mt-20 flex justify-center">
          <Reveal delay={400}>
            <button 
              onClick={handleSubscribe}
              className="group flex items-center gap-6 rounded-full bg-[#2D4A32] pl-8 pr-2 py-2 transition-all duration-500 ease-[cubic-bezier(0.32,0.72,0,1)] hover:bg-black active:scale-[0.98]"
            >
              <span className="text-sm font-medium uppercase tracking-[0.1em] text-white">Start Subscription</span>
              <div className="flex size-12 items-center justify-center rounded-full bg-white/20 transition-transform duration-500 group-hover:translate-x-1 group-hover:bg-white/30 group-hover:scale-105">
                <ArrowRight className="size-5 text-white" strokeWidth={1.5} />
              </div>
            </button>
          </Reveal>
        </div>
      </section>
    </div>
  )
}
