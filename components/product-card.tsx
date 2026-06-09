'use client'

import Link from 'next/link'
import Image from 'next/image'
import { useState } from 'react'
import { motion } from 'motion/react'
import { Plus } from 'lucide-react'
import { useCart } from '@/components/cart/cart-provider'
import type { Product } from '@/lib/products'
import { formatPrice } from '@/lib/products'

export function ProductCard({
  product,
  index = 0,
}: {
  product: Product
  index?: number
}) {
  const [hover, setHover] = useState(false)
  const { addItem } = useCart()

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
    <motion.div
      initial={{ opacity: 0, y: 24 }}
      whileInView={{ opacity: 1, y: 0 }}
      viewport={{ once: true, amount: 0.2 }}
      transition={{ duration: 0.6, delay: (index % 4) * 0.08 }}
      className="group"
      onMouseEnter={() => setHover(true)}
      onMouseLeave={() => setHover(false)}
    >
      <Link href={`/shop/${product.slug}`} className="block">
        <div className="relative aspect-[4/5] overflow-hidden rounded-xl bg-card">
          <Image
            src={product.image || '/placeholder.svg'}
            alt={product.imageAlt}
            fill
            sizes="(max-width: 640px) 50vw, 25vw"
            className="object-cover transition-all duration-700 group-hover:scale-105"
            style={{ opacity: hover ? 0 : 1 }}
          />
          <Image
            src={product.hoverImage || '/placeholder.svg'}
            alt={product.hoverImageAlt}
            fill
            sizes="(max-width: 640px) 50vw, 25vw"
            className="object-cover transition-all duration-700 group-hover:scale-105"
            style={{ opacity: hover ? 1 : 0 }}
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

        <div className="mt-3 flex items-baseline justify-between gap-2">
          <h3 className="font-serif text-lg leading-tight">{product.name}</h3>
          <p className="text-sm text-muted-foreground">
            from {formatPrice(product.basePrice)}
          </p>
        </div>
        <p className="mt-0.5 text-sm text-muted-foreground">{product.tagline}</p>
      </Link>
    </motion.div>
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
