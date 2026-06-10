'use client'

import Link from 'next/link'
import Image from 'next/image'
import { ArrowRight } from 'lucide-react'
import { OCCASIONS } from '@/lib/products'
import { cn } from '@/lib/utils'
import { Reveal } from '@/components/kinetic-text'

// Map occasions to specific layout spans for the Asymmetrical Bento
const BENTO_MAP: Record<string, { span: string; img: string; delay: number }> = {
  'Birthday': { span: 'md:col-span-8 md:row-span-2', img: '/products/somerville-sunset.png', delay: 0.1 },
  'Anniversary': { span: 'md:col-span-4 md:row-span-1', img: '/products/back-bay-blush.png', delay: 0.2 },
  'Just Because': { span: 'md:col-span-4 md:row-span-1', img: '/products/sunday-market.png', delay: 0.3 },
  'Sympathy': { span: 'md:col-span-6 md:row-span-1', img: '/products/with-sympathy.png', delay: 0.1 },
  'New Baby': { span: 'md:col-span-6 md:row-span-1', img: '/products/new-baby-cloud.png', delay: 0.2 },
}

export default function OccasionsPage() {
  return (
    <div className="min-h-[100dvh] w-full bg-[#FAF7F2] pb-32">
      
      {/* Header Section */}
      <section className="mx-auto max-w-[90rem] px-4 pt-24 sm:px-6 md:pt-32 lg:px-8">
        <div className="max-w-3xl">
          <Reveal>
            <div className="inline-flex items-center rounded-full border border-black/10 px-4 py-1.5 text-[10px] font-medium tracking-[0.2em] uppercase text-black/60">
              Occasions
            </div>
          </Reveal>
          <Reveal delay={100}>
            <h1 className="mt-8 font-serif text-5xl leading-[1.05] tracking-tight sm:text-6xl md:text-7xl">
              For every moment, <br/>
              <span className="italic text-black/40">beautifully said.</span>
            </h1>
          </Reveal>
        </div>
      </section>

      {/* The Asymmetrical Bento Grid */}
      <section className="mx-auto mt-16 max-w-[90rem] px-4 sm:px-6 lg:px-8">
        <div className="grid grid-cols-1 gap-6 md:grid-cols-12 md:auto-rows-[340px]">
          {OCCASIONS.map((occasion) => {
            const config = BENTO_MAP[occasion] || { span: 'md:col-span-4', img: '/placeholder.svg', delay: 0 }
            
            return (
              <Reveal
                key={occasion}
                delay={config.delay}
                className={cn(
                  "group relative w-full h-[400px] md:h-auto overflow-hidden rounded-[2.5rem] bg-black/5 p-1.5 ring-1 ring-black/5",
                  config.span
                )}
              >
                <div className="relative h-full w-full overflow-hidden rounded-[calc(2.5rem-0.375rem)] shadow-[inset_0_1px_1px_rgba(255,255,255,0.2)] bg-black">
                  <Image 
                    src={config.img} 
                    alt={occasion} 
                    fill 
                    className="object-cover opacity-80 transition-transform duration-[1.5s] ease-[cubic-bezier(0.32,0.72,0,1)] group-hover:scale-105 group-hover:opacity-60"
                  />
                  
                  {/* Inner Content overlay */}
                  <div className="absolute inset-0 flex flex-col justify-between p-8 sm:p-10">
                    <div className="flex justify-end">
                      <div className="flex size-12 items-center justify-center rounded-full bg-white/10 backdrop-blur-md transition-transform duration-700 group-hover:scale-110 group-hover:bg-white/20">
                        <ArrowRight className="size-5 text-white" strokeWidth={1.5} />
                      </div>
                    </div>
                    <div>
                      <h2 className="font-serif text-4xl text-white sm:text-5xl">{occasion}</h2>
                    </div>
                  </div>
                  
                  <Link href={`/shop?occasion=${encodeURIComponent(occasion)}`} className="absolute inset-0 z-10">
                    <span className="sr-only">Shop {occasion}</span>
                  </Link>
                </div>
              </Reveal>
            )
          })}
        </div>
      </section>
    </div>
  )
}
