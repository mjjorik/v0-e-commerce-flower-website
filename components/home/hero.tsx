'use client'

import { useRef } from 'react'
import Link from 'next/link'
import { gsap } from 'gsap'
import { ScrollTrigger } from 'gsap/dist/ScrollTrigger'
import { useGSAP } from '@gsap/react'
import { ArrowRight } from 'lucide-react'
import { KineticText } from '@/components/kinetic-text'
import { SmartImage } from '@/components/smart-image'
import { BRAND } from '@/lib/brand'

if (typeof window !== 'undefined') {
  gsap.registerPlugin(ScrollTrigger)
}

export function Hero() {
  const containerRef = useRef<HTMLElement>(null)
  const imageRef = useRef<HTMLDivElement>(null)

  useGSAP(() => {
    gsap.fromTo(
      imageRef.current,
      { clipPath: 'inset(100% 0% 0% 0%)', scale: 1.08, y: 48 },
      {
        clipPath: 'inset(0% 0% 0% 0%)',
        scale: 1,
        y: 0,
        duration: 1.6,
        ease: 'expo.inOut',
        delay: 0.15,
      },
    )
  }, { scope: containerRef })

  return (
    <section
      ref={containerRef}
      className="relative flex min-h-[92dvh] flex-col justify-center overflow-hidden pb-16 pt-32 sm:pt-40"
    >
      {/* soft botanical wash */}
      <div
        aria-hidden
        className="pointer-events-none absolute -right-40 -top-20 -z-10 size-[42rem] rounded-full opacity-60 blur-3xl"
        style={{
          background:
            'radial-gradient(circle, color-mix(in oklab, var(--sage) 60%, transparent), transparent 70%)',
        }}
      />

      <div className="relative z-10 mx-auto w-full max-w-[100rem] px-6 lg:px-12">
        <div className="grid grid-cols-1 items-end gap-12 lg:grid-cols-12">
          {/* SEO headline + intro */}
          <div className="lg:col-span-8">
            <div className="space-y-9">
              <div className="inline-flex items-center gap-2 rounded-full border border-ink/10 px-4 py-1.5 text-[10px] font-semibold uppercase tracking-[0.3em] text-ink/45">
                <span className="size-1.5 rounded-full bg-terracotta" />
                Fresh flowers · {BRAND.city}
              </div>

              <h1 className="font-serif text-[clamp(3.25rem,9.5vw,8rem)] font-medium leading-[0.9] tracking-tighter text-ink">
                <KineticText text="Beautiful flowers." className="block" delay={0.1} />
                <span className="mt-2 block">
                  <KineticText text="Honest" className="inline-block italic text-terracotta" delay={0.4} />
                  <KineticText text="prices." className="ml-[0.2em] inline-block" delay={0.5} />
                </span>
              </h1>

              <div className="flex flex-col gap-8 pt-2 md:flex-row md:items-center">
                <p className="max-w-sm text-lg font-medium leading-snug text-ink/55 text-pretty">
                  Farm-fresh bouquets and weekly subscriptions, hand-delivered
                  same-day across Greater Boston. Order by {BRAND.cutoff}.
                </p>

                <Link href="/shop" className="btn-primary btn-lg group w-max">
                  Shop Bouquets
                  <ArrowRight className="btn-arrow size-4" strokeWidth={2} />
                </Link>
              </div>
            </div>
          </div>

          {/* immersive visual */}
          <div className="relative z-10 lg:col-span-4 lg:-mb-20">
            <div
              ref={imageRef}
              className="relative ml-auto aspect-[3/4] w-full max-w-md overflow-hidden rounded-2xl bg-card shadow-[0_50px_120px_-30px_rgba(20,36,22,0.3)] ring-1 ring-black/5 lg:translate-x-6 lg:-rotate-2"
            >
              <SmartImage
                src="/hero/hero-bouquet.png"
                alt="Boutique Wildflower bouquet hand-tied in Boston"
                fill
                priority
                sizes="(max-width: 1024px) 100vw, 30vw"
                fallbackLabel="Wildflower"
                className="object-cover transition-transform duration-[2.5s] hover:scale-105"
              />
              <div className="absolute inset-0 bg-gradient-to-t from-ink/25 to-transparent" />
            </div>

            {/* geo badge */}
            <div className="absolute bottom-24 -left-8 z-30 hidden rotate-2 rounded-xl border border-white/40 bg-linen/90 p-5 shadow-xl ring-1 ring-black/5 backdrop-blur-xl xl:block">
              <p className="mb-1 text-[10px] font-bold uppercase tracking-widest text-ink/40">
                Serving
              </p>
              <p className="font-serif text-lg leading-tight text-ink">
                Back Bay, Cambridge <br />& Somerville
              </p>
            </div>
          </div>
        </div>
      </div>
    </section>
  )
}
