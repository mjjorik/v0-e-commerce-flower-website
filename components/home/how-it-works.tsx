'use client'

import { motion, useReducedMotion } from 'motion/react'

const STEPS = [
  {
    n: '01',
    title: 'Pick your bouquet',
    body: 'Browse the line-up or start a subscription. Choose a size — Petite, Classic or Grand.',
  },
  {
    n: '02',
    title: 'Tell us when & where',
    body: 'Add a delivery date, a time slot and a gift message. Same-day if you order by 1 PM.',
  },
  {
    n: '03',
    title: 'We hand-deliver it',
    body: 'Our local couriers bring it fresh to the door, anywhere across Greater Boston.',
  },
]

export function HowItWorks() {
  const reduce = useReducedMotion()
  return (
    <section className="bg-primary py-16 text-primary-foreground sm:py-24">
      <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <h2 className="mb-12 max-w-md text-balance font-serif text-3xl tracking-tight sm:text-5xl">
          How it works
        </h2>
        <div className="grid gap-10 sm:grid-cols-3 sm:gap-8">
          {STEPS.map((step, i) => (
            <div key={step.n}>
              <motion.p
                initial={reduce ? false : { opacity: 0, y: 40 }}
                whileInView={{ opacity: 1, y: 0 }}
                viewport={{ once: true, amount: 0.6 }}
                transition={{
                  type: 'spring',
                  stiffness: 120,
                  damping: 18,
                  delay: i * 0.12,
                }}
                className="font-serif text-6xl text-primary-foreground/40 sm:text-7xl"
              >
                {step.n}
              </motion.p>
              <h3 className="mt-4 font-serif text-2xl">{step.title}</h3>
              <p className="mt-2 max-w-xs text-pretty leading-relaxed text-primary-foreground/70">
                {step.body}
              </p>
            </div>
          ))}
        </div>
      </div>
    </section>
  )
}
