'use client'

import Link from 'next/link'
import Image from 'next/image'
import { useRef } from 'react'
import { Plus } from 'lucide-react'
import { gsap } from 'gsap'
import { ScrollTrigger } from 'gsap/dist/ScrollTrigger'
import { useGSAP } from '@gsap/react'
import { useCart } from '@/components/cart/cart-provider'
import type { Product } from '@/lib/products'
import { formatPrice } from '@/lib/products'

if (typeof window !== 'undefined') {
  gsap.registerPlugin(ScrollTrigger)
}

export function ProductCard({
  product,
  index = 0,
}: {
  product: Product
  index?: number
}) {
  const { addItem } = useCart()
  const cardRef = useRef<HTMLDivElement>(null)

  useGSAP(() => {
    if (!cardRef.current) return

    gsap.fromTo(cardRef.current, 
      { opacity: 0, y: 24 },
      {
        opacity: 1,
        y: 0,
        duration: 0.6,
        ease: "power2.out",
        delay: (index % 4) * 0.08,
        scrollTrigger: {
          trigger: cardRef.current,
          start: 'top 92%',
          toggleActions: 'play none none none',
        }
      }
    )
  }, { scope: cardRef })

  const quickAdd = (e: React.MouseEvent) => {
    e.preventDefault()
    const size = product.sizes[1] ?? product.sizes[0]
    addItem({
      productSlug: product.slug,
      name: product.name,
      sizeKey: size.key,
      sizeLabel: size.label,
      price: size.price,
      image: product.image,
      imageAlt: product.imageAlt,
    })
  }

  return (
    <div ref={cardRef} className="group opacity-0">
      <Link href={`/shop/${product.slug}`} className="block">
        <div className="relative aspect-[4/5] overflow-hidden rounded-xl bg-card">
          <Image
            src={product.image || '/placeholder.svg'}
            alt={product.imageAlt}
            fill
            sizes="(max-width: 640px) 50vw, 25vw"
            className="object-cover transition-all duration-700 md:group-hover:scale-105"
          />
          <Image
            src={product.hoverImage || '/placeholder.svg'}
            alt={product.hoverImageAlt}
            fill
            sizes="(max-width: 640px) 50vw, 25vw"
            className="object-cover opacity-0 transition-all duration-700 md:group-hover:opacity-100 md:group-hover:scale-105"
          />

          {/* badges */}
          <div className="absolute left-3 top-3 flex flex-col gap-1.5">
            {product.bestseller && (
              <span className="rounded-full bg-primary px-2.5 py-1 text-[11px] font-medium text-primary-foreground">
                Bestseller
              </span>
            )}
            {product.seasonal && (
              <span className="rounded-full bg-terracotta px-2.5 py-1 text-[11px] font-medium text-terracotta-foreground">
                Seasonal
              </span>
            )}
          </div>

          {/* quick add */}
          <button
            onClick={quickAdd}
            aria-label={`Quick add ${product.name}`}
            className="absolute bottom-3 right-3 flex items-center gap-1.5 rounded-full bg-background/95 px-4 py-2.5 text-sm font-medium shadow-sm backdrop-blur transition-all duration-300 hover:bg-primary hover:text-primary-foreground md:translate-y-3 md:opacity-0 md:group-hover:translate-y-0 md:group-hover:opacity-100"
          >
            <Plus className="size-4" /> Add
          </button>
        </div>

        <div className="mt-3 flex items-baseline justify-between gap-2 px-1">
          <h3 className="font-serif text-lg leading-tight">{product.name}</h3>
          <p className="text-sm text-muted-foreground">
            from {formatPrice(product.basePrice)}
          </p>
        </div>
        <p className="mt-0.5 px-1 text-sm text-muted-foreground">{product.tagline}</p>
      </Link>
    </div>
  )
}

export function ProductCardSkeleton() {
  return (
    <div className="animate-pulse">
      <div className="aspect-[4/5] rounded-xl bg-muted" />
      <div className="mt-3 h-4 w-2/3 rounded bg-muted" />
      <div className="mt-2 h-3 w-1/2 rounded bg-muted" />
    </div>
  )
}
