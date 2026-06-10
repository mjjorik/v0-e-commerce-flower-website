import Link from 'next/link'
import Image from 'next/image'
import { Reveal } from '@/components/kinetic-text'
import { BRAND } from '@/lib/brand'

const InstagramIcon = ({ className }: { className?: string }) => (
  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" className={className}><rect width="20" height="20" x="2" y="2" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" x2="17.51" y1="6.5" y2="6.5"/></svg>
)

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
        <div className="mb-8 flex items-center justify-between gap-4">
          <Reveal>
            <h2 className="font-serif text-3xl tracking-tight sm:text-4xl">
              From the studio
            </h2>
          </Reveal>
          <Reveal delay={150}>
            <a
              href="https://instagram.com"
              className="inline-flex items-center gap-2 text-sm underline underline-offset-4 hover:opacity-70"
            >
              <InstagramIcon className="size-4" />
              {BRAND.handle}
            </a>
          </Reveal>
        </div>

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
