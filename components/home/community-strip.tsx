import Link from 'next/link'
import Image from 'next/image'
import { Instagram } from 'lucide-react'
import { Reveal } from '@/components/kinetic-text'
import { BRAND } from '@/lib/brand'

const SHOTS = [
  { src: '/instagram/ig-1.png', span: 'row-span-2' },
  { src: '/instagram/ig-2.png', span: '' },
  { src: '/instagram/ig-3.png', span: '' },
  { src: '/instagram/ig-4.png', span: 'row-span-2' },
  { src: '/instagram/ig-5.png', span: '' },
  { src: '/instagram/ig-6.png', span: '' },
]

export function CommunityStrip() {
  return (
    <section className="py-16 sm:py-20">
      <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <Reveal className="mb-8 flex items-center justify-between gap-4">
          <h2 className="font-serif text-3xl tracking-tight sm:text-4xl">
            From the studio
          </h2>
          <a
            href="https://instagram.com"
            className="inline-flex items-center gap-2 text-sm underline underline-offset-4 hover:opacity-70"
          >
            <Instagram className="size-4" />
            {BRAND.handle}
          </a>
        </Reveal>

        <div className="grid auto-rows-[150px] grid-cols-2 gap-3 sm:auto-rows-[180px] sm:grid-cols-4">
          {SHOTS.map((shot, i) => (
            <Link
              key={i}
              href="/shop"
              className={`group relative overflow-hidden rounded-xl ${shot.span}`}
            >
              <Image
                src={shot.src || '/placeholder.svg'}
                alt="Wildflower bouquet shared on Instagram"
                fill
                sizes="(max-width: 640px) 50vw, 25vw"
                className="object-cover transition-transform duration-700 group-hover:scale-105"
              />
            </Link>
          ))}
        </div>
      </div>
    </section>
  )
}
