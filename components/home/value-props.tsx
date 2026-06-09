'use client'

import { motion } from 'motion/react'
import { Truck, Sprout, BadgeDollarSign } from 'lucide-react'

const PROPS = [
  {
    icon: Truck,
    title: 'Same-Day Delivery',
    body: 'Order by 1 PM and it lands on their doorstep today, across Greater Boston.',
  },
  {
    icon: Sprout,
    title: 'Farm-Fresh Guarantee',
    body: 'Cut this week from New England growers. If it wilts early, we replace it.',
  },
  {
    icon: BadgeDollarSign,
    title: '$50–$130, No Markups',
    body: 'Honest pricing on every stem. The kind of flowers you can send on a Tuesday.',
  },
]

export function ValueProps() {
  return (
    <section className="bg-card py-16 sm:py-20">
      <div className="mx-auto grid max-w-7xl gap-10 px-4 sm:px-6 lg:grid-cols-3 lg:gap-8 lg:px-8">
        {PROPS.map((p, i) => (
          <motion.div
            key={p.title}
            initial={{ opacity: 0, y: 28 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true, amount: 0.4 }}
            transition={{ duration: 0.6, delay: i * 0.12 }}
            className="flex flex-col gap-3"
          >
            <p.icon className="size-6 text-primary" strokeWidth={1.5} />
            <h3 className="font-serif text-2xl tracking-tight">{p.title}</h3>
            <p className="text-pretty leading-relaxed text-foreground/70">
              {p.body}
            </p>
          </motion.div>
        ))}
      </div>
    </section>
  )
}
