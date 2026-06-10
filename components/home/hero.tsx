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

  useGSAP(() => {
    // Ultra-Luxe reveal of the main image
    gsap.fromTo(imageRef.current, 
      { 
        clipPath: 'inset(100% 0% 0% 0%)',
        scale: 1.1,
        y: 60
      },
      {
        clipPath: 'inset(0% 0% 0% 0%)',
        scale: 1,
        y: 0,
        duration: 2,
        ease: "expo.inOut",
        delay: 0.2
      }
    )
  }, { scope: containerRef })

  return (
    <section
      ref={containerRef}
      className="relative min-h-[100dvh] flex flex-col justify-center overflow-hidden pt-32 pb-12 sm:pt-40"
    >
      <div className="mx-auto max-w-[100rem] w-full px-6 lg:px-12 relative z-10">
        <div className="grid grid-cols-1 lg:grid-cols-12 gap-12 items-end">
          
          {/* Main SEO Content */}
          <div className="lg:col-span-8">
            <div className="space-y-10">
              <div className="inline-flex items-center rounded-full border border-ink/10 px-4 py-1.5 text-[10px] font-bold uppercase tracking-[0.3em] text-ink/40">
                Fresh flowers · {BRAND.city}
              </div>
              
              <h1 className="font-serif text-[clamp(3.5rem,10vw,8.5rem)] font-medium leading-[0.88] tracking-tighter text-ink perspective-[1200px]">
                <KineticText text="Beautiful flowers." className="block" delay={100} />
                <div className="block mt-2">
                   <KineticText text="Honest" className="inline-block italic text-clay" delay={400} />
                   <KineticText text="prices." className="inline-block ml-[0.2em]" delay={500} />
                </div>
              </h1>

              <div className="flex flex-col gap-10 md:flex-row md:items-center pt-4">
                <p className="max-w-sm text-lg font-medium leading-[1.4] text-ink/50 text-pretty">
                  Farm-fresh bouquets and weekly flower subscriptions, delivered same-day across Greater Boston. Order by 1 PM.
                </p>
                
                {/* Luxe Button-in-Button */}
                <Link
                  href="/shop"
                  className="group flex items-center gap-10 rounded-full bg-ink pl-8 pr-2 py-2 transition-all duration-700 ease-[cubic-bezier(0.23,1,0.32,1)] hover:bg-clay active:scale-[0.98] w-max shadow-2xl shadow-ink/20"
                >
                  <span className="text-[11px] font-bold uppercase tracking-[0.2em] text-linen">Shop Bouquets</span>
                  <div className="flex size-12 items-center justify-center rounded-full bg-linen/20 transition-transform duration-500 group-hover:translate-x-1 group-hover:scale-105">
                    <ArrowRight className="size-5 text-linen" strokeWidth={1.5} />
                  </div>
                </Link>
              </div>
            </div>
          </div>

          {/* Overlapping Immersive Visual */}
          <div className="lg:col-span-4 relative z-10 lg:-mb-20">
            <div ref={imageRef} className="relative aspect-[3/4] w-full max-w-md ml-auto overflow-hidden rounded-[3rem] bg-card ring-1 ring-black/5 shadow-[0_50px_120px_rgba(0,0,0,0.12)] lg:-rotate-2 lg:translate-x-8">
              <Image
                src="/hero/hero-bouquet.png"
                alt="Boutique Wildflower bouquet in Boston"
                fill
                priority
                sizes="(max-width: 1024px) 100vw, 30vw"
                className="object-cover transition-transform duration-[2.5s] hover:scale-105"
              />
              <div className="absolute inset-0 bg-gradient-to-t from-ink/20 to-transparent" />
            </div>
            
            {/* Geo SEO Badge */}
            <div className="absolute -left-10 bottom-24 bg-linen/90 backdrop-blur-xl border border-white/40 p-5 rounded-2xl shadow-xl ring-1 ring-black/5 hidden xl:block rotate-3 z-30">
               <p className="text-[10px] font-bold uppercase tracking-widest text-ink/40 mb-1">Serving</p>
               <p className="font-serif text-lg text-ink leading-tight">Back Bay, Cambridge <br/>& Somerville</p>
            </div>
          </div>

        </div>
      </div>
    </section>
  )
}
