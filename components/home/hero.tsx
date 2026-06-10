'use client'

import { useRef } from 'react'
import Link from 'next/link'
import Image from 'next/image'
import { gsap } from 'gsap'
import { ScrollTrigger } from 'gsap/dist/ScrollTrigger'
import { useGSAP } from '@gsap/react'
import { ArrowRight } from 'lucide-react'
import { KineticText } from '@/components/kinetic-text'
import { BRAND } from '@/lib/brand'

if (typeof window !== 'undefined') {
  gsap.registerPlugin(ScrollTrigger)
}

export function Hero() {
  const containerRef = useRef<HTMLElement>(null)
  const imageRef = useRef<HTMLDivElement>(null)
  const petal1Ref = useRef<HTMLDivElement>(null)
  const petal2Ref = useRef<HTMLDivElement>(null)

  useGSAP(() => {
    // Elegant reveal of the image (Original v0 behavior but GSAP stable)
    gsap.fromTo(imageRef.current, 
      { clipPath: 'inset(8% 8% 8% 8% round 24px)', opacity: 0 },
      { clipPath: 'inset(0% 0% 0% 0% round 24px)', opacity: 1, duration: 1.1, ease: "power2.out", delay: 0.2 }
    )

    // Floating petals parallax (v0 original effect)
    gsap.to(petal1Ref.current, {
      y: -60,
      scrollTrigger: {
        trigger: containerRef.current,
        start: 'top top',
        end: 'bottom top',
        scrub: true
      }
    })

    gsap.to(petal2Ref.current, {
      y: 120,
      scrollTrigger: {
        trigger: containerRef.current,
        start: 'top top',
        end: 'bottom top',
        scrub: true
      }
    })
  }, { scope: containerRef })

  return (
    <section
      ref={containerRef}
      className="relative overflow-hidden bg-background px-4 pb-10 pt-8 sm:px-6 lg:px-8 lg:pb-20 lg:pt-16"
    >
      <div className="mx-auto grid max-w-7xl items-center gap-8 lg:grid-cols-[1.1fr_0.9fr] lg:gap-12">
        <div className="relative z-10">
          <p className="mb-5 text-xs uppercase tracking-[0.28em] text-muted-foreground">
            Fresh flowers · {BRAND.city}
          </p>

          <h1 className="font-serif text-[clamp(2.75rem,9vw,6.5rem)] font-medium leading-[0.95] tracking-tight text-balance">
            <KineticText as="span" text="Beautiful flowers." className="block" />
            <KineticText
              as="span"
              text="Honest prices."
              className="block italic text-terracotta"
              delay={350}
            />
          </h1>

          <p className="mt-6 max-w-md text-pretty text-base leading-relaxed text-foreground/75 sm:text-lg">
            Farm-fresh bouquets and weekly subscriptions, delivered same-day
            across Greater Boston. No markups, no fuss — just good flowers,
            $50 to $130.
          </p>

          <div className="mt-8 flex flex-wrap items-center gap-4">
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
          </div>
        </div>

        <div className="relative">
          <div
            ref={imageRef}
            className="relative aspect-[4/5] overflow-hidden rounded-3xl bg-card sm:aspect-[5/6]"
          >
            <Image
              src="/hero/hero-bouquet.png"
              alt="Editorial photograph of a loose seasonal bouquet"
              fill
              priority
              sizes="(max-width: 1024px) 100vw, 45vw"
              className="object-cover"
            />
          </div>

          <div
            ref={petal1Ref}
            className="pointer-events-none absolute -left-6 -top-6 size-24 rounded-full bg-sage/50 blur-[2px] sm:size-32"
            aria-hidden
          />
          <div
            ref={petal2Ref}
            className="pointer-events-none absolute -bottom-8 -right-4 size-20 rounded-[40%_60%_55%_45%] bg-terracotta/30 sm:size-28"
            aria-hidden
          />
        </div>
      </div>
    </section>
  )
}
