'use client'

import Link from 'next/link'
import Image from 'next/image'
import { useRef } from 'react'
import { motion, useScroll, useTransform, useReducedMotion } from 'motion/react'
import { ArrowRight } from 'lucide-react'
import { KineticText } from '@/components/kinetic-text'
import { BRAND } from '@/lib/brand'

export function Hero() {
  const ref = useRef<HTMLElement>(null)
  const reduce = useReducedMotion()
  const { scrollYProgress } = useScroll({
    target: ref,
    offset: ['start start', 'end start'],
  })
  const yImage = useTransform(scrollYProgress, [0, 1], [0, reduce ? 0 : 80])
  const yPetal1 = useTransform(scrollYProgress, [0, 1], [0, reduce ? 0 : -60])
  const yPetal2 = useTransform(scrollYProgress, [0, 1], [0, reduce ? 0 : 120])

  return (
    <section
      ref={ref}
      className="relative overflow-hidden bg-background px-4 pb-10 pt-8 sm:px-6 lg:px-8 lg:pb-20 lg:pt-16"
    >
      <div className="mx-auto grid max-w-7xl items-center gap-8 lg:grid-cols-[1.1fr_0.9fr] lg:gap-12">
        {/* copy */}
        <div className="relative z-10">
          <motion.p
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            transition={{ delay: 0.1 }}
            className="mb-5 text-xs uppercase tracking-[0.28em] text-muted-foreground"
          >
            Fresh flowers · {BRAND.city}
          </motion.p>

          <h1 className="font-serif text-[clamp(2.75rem,9vw,6.5rem)] font-medium leading-[0.95] tracking-tight text-balance">
            <KineticText as="span" text="Beautiful flowers." className="block" />
            <KineticText
              as="span"
              text="Honest prices."
              className="block italic text-terracotta"
              delay={0.35}
            />
          </h1>

          <motion.p
            initial={{ opacity: 0, y: 16 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ delay: 0.9, duration: 0.7 }}
            className="mt-6 max-w-md text-pretty text-base leading-relaxed text-foreground/75 sm:text-lg"
          >
            Farm-fresh bouquets and weekly subscriptions, delivered same-day
            across Greater Boston. No markups, no fuss — just good flowers,
            $50 to $130.
          </motion.p>

          <motion.div
            initial={{ opacity: 0, y: 16 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ delay: 1.05, duration: 0.7 }}
            className="mt-8 flex flex-wrap items-center gap-4"
          >
            <Link
              href="/shop"
              className="group inline-flex items-center gap-2 rounded-full bg-primary px-7 py-4 text-sm text-primary-foreground transition-transform hover:-translate-y-0.5"
            >
              Shop Bouquets
              <ArrowRight className="size-4 transition-transform group-hover:translate-x-1" />
            </Link>
            <Link
              href="/subscriptions"
              className="text-sm underline decoration-1 underline-offset-4 transition-opacity hover:opacity-70"
            >
              Start a subscription
            </Link>
          </motion.div>
        </div>

        {/* image with layered depth + parallax */}
        <motion.div style={{ y: yImage }} className="relative">
          <motion.div
            initial={{ clipPath: 'inset(8% 8% 8% 8% round 24px)', opacity: 0 }}
            animate={{ clipPath: 'inset(0% 0% 0% 0% round 24px)', opacity: 1 }}
            transition={{ duration: 1.1, ease: [0.22, 1, 0.36, 1], delay: 0.2 }}
            className="relative aspect-[4/5] overflow-hidden rounded-3xl bg-card sm:aspect-[5/6]"
          >
            <Image
              src="/hero/hero-bouquet.png"
              alt="Editorial photograph of a loose seasonal bouquet in soft natural light"
              fill
              priority
              sizes="(max-width: 1024px) 100vw, 45vw"
              className="object-cover"
            />
          </motion.div>

          {/* floating petal accents */}
          <motion.div
            style={{ y: yPetal1 }}
            className="pointer-events-none absolute -left-6 -top-6 size-24 rounded-full bg-sage/50 blur-[2px] sm:size-32"
            aria-hidden
          />
          <motion.div
            style={{ y: yPetal2 }}
            className="pointer-events-none absolute -bottom-8 -right-4 size-20 rounded-[40%_60%_55%_45%] bg-terracotta/30 sm:size-28"
            aria-hidden
          />
        </motion.div>
      </div>
    </section>
  )
}
