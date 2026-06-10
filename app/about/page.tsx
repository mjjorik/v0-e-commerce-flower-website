'use client'

import Image from 'next/image'
import { Reveal } from '@/components/kinetic-text'

export default function AboutPage() {
  return (
    <div className="relative min-h-[100dvh] w-full overflow-hidden bg-[#FAF7F2]">
      {/* Subtle Noise Overlay */}
      <div className="pointer-events-none fixed inset-0 z-50 opacity-[0.03] mix-blend-multiply" style={{ backgroundImage: 'url("data:image/svg+xml,%3Csvg viewBox=\'0 0 200 200\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cfilter id=\'noiseFilter\'%3E%3CfeTurbulence type=\'fractalNoise\' baseFrequency=\'0.65\' numOctaves=\'3\' stitchTiles=\'stitch\'/%3E%3C/filter%3E%3Crect width=\'100%25\' height=\'100%25\' filter=\'url(%23noiseFilter)\'/%3E%3C/svg%3E")' }}></div>

      <section className="mx-auto max-w-[90rem] px-4 py-24 sm:px-6 md:py-32 lg:px-8 xl:py-40">
        <div className="flex flex-col gap-16 lg:flex-row lg:items-start lg:gap-24">
          
          {/* Left: Editorial Typography (The Editorial Split) */}
          <div className="flex-1 pt-12 lg:sticky lg:top-32">
            <Reveal>
              <div className="inline-flex items-center rounded-full border border-black/10 px-4 py-1.5 text-[10px] font-medium tracking-[0.2em] uppercase text-black/60">
                The Studio
              </div>
            </Reveal>
            
            <Reveal delay={100}>
              <h1 className="mt-8 font-serif text-5xl leading-[1.05] tracking-tight sm:text-6xl md:text-7xl lg:text-8xl">
                We don't do <br />
                <span className="text-black/30 italic">baby's breath.</span>
              </h1>
            </Reveal>

            <Reveal delay={200}>
              <div className="mt-12 max-w-xl space-y-8 text-lg font-light leading-relaxed text-black/70 sm:text-xl">
                <p>
                  Wildflower was born out of a profound frustration with generic, overpriced flower delivery services that lack taste. We believe flowers shouldn't be an intimidating luxury reserved only for Valentine's Day.
                </p>
                <p>
                  By abandoning the traditional retail storefront, we operate a highly efficient, closed-door studio in the heart of Boston. This allows us to pour every dollar back into sourcing the most interesting, textural stems directly from our partner farms.
                </p>
              </div>
            </Reveal>
          </div>

          {/* Right: Asymmetrical Image Cascade (Double-Bezel Architecture) */}
          <div className="flex flex-1 flex-col gap-8 sm:gap-12 lg:mt-32">
            
            <Reveal className="relative aspect-[3/4] w-full max-w-lg self-end rounded-[2.5rem] bg-black/5 p-2 ring-1 ring-black/5">
              <div className="relative h-full w-full overflow-hidden rounded-[calc(2.5rem-0.5rem)] shadow-[inset_0_1px_1px_rgba(255,255,255,0.4)]">
                <Image 
                  src="/instagram/ig-1.png" 
                  alt="Florist working in Boston studio" 
                  fill 
                  className="object-cover"
                />
              </div>
            </Reveal>

            <Reveal className="relative aspect-square w-full max-w-md self-start rounded-[2.5rem] bg-black/5 p-2 ring-1 ring-black/5 lg:-mt-32" delay={150}>
              <div className="relative h-full w-full overflow-hidden rounded-[calc(2.5rem-0.5rem)] shadow-[inset_0_1px_1px_rgba(255,255,255,0.4)]">
                <Image 
                  src="/instagram/ig-2.png" 
                  alt="Fresh ranunculus close up" 
                  fill 
                  className="object-cover"
                />
              </div>
            </Reveal>

            <Reveal className="relative w-full max-w-xl self-center rounded-[2.5rem] bg-white p-10 ring-1 ring-black/5 sm:p-16 lg:mt-16">
              <h3 className="font-serif text-3xl">The Team</h3>
              <p className="mt-6 text-black/60 leading-relaxed text-lg">
                We are a small, obsessed team of designers, florists, and drivers who believe in doing one thing exceptionally well. No fluff, no filler—just honest flowers delivered with absolute precision.
              </p>
            </Reveal>

          </div>

        </div>
      </section>
    </div>
  )
}
