import Link from 'next/link'
import { Reveal } from '@/components/kinetic-text'
import { ProductCard } from '@/components/product-card'
import { PRODUCTS } from '@/lib/products'

export function FeaturedGrid() {
  const featured = PRODUCTS.filter((p) => p.bestseller).slice(0, 4)
  const extras = PRODUCTS.filter((p) => !p.bestseller).slice(0, 2)
  const items = [...featured, ...extras].slice(0, 6)

  return (
    <section className="py-16 sm:py-20">
      <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div className="mb-10 max-w-xl">
          <Reveal>
            <p className="mb-2 text-xs uppercase tracking-[0.28em] text-muted-foreground">
              The line-up
            </p>
          </Reveal>
          <Reveal delay={100}>
            <h2 className="text-balance font-serif text-3xl tracking-tight sm:text-5xl">
              Bouquets people keep coming back for
            </h2>
          </Reveal>
        </div>

        <div className="grid grid-cols-2 gap-x-4 gap-y-10 lg:grid-cols-4">
          {items.map((product, i) => (
            <ProductCard key={product.slug} product={product} index={i} />
          ))}
        </div>

        <div className="mt-12 text-center">
          <Reveal delay={300}>
            <Link
              href="/shop"
              className="inline-flex rounded-full border border-primary px-7 py-3.5 text-sm text-primary transition-colors hover:bg-primary hover:text-primary-foreground"
            >
              Shop all bouquets
            </Link>
          </Reveal>
        </div>
      </div>
    </section>
  )
}
