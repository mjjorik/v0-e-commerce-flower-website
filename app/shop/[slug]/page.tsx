import type { Metadata } from 'next'
import { notFound } from 'next/navigation'
import Link from 'next/link'
import { ChevronRight } from 'lucide-react'
import { PRODUCTS, getProduct } from '@/lib/products'
import { ProductDetail } from '@/components/shop/product-detail'
import { ProductCard } from '@/components/product-card'

export function generateStaticParams() {
  return PRODUCTS.map((p) => ({ slug: p.slug }))
}

export async function generateMetadata({
  params,
}: {
  params: Promise<{ slug: string }>
}): Promise<Metadata> {
  const { slug } = await params
  const product = getProduct(slug)
  if (!product) return { title: 'Not found' }
  return {
    title: product.name,
    description: product.tagline,
  }
}

export default async function ProductPage({
  params,
}: {
  params: Promise<{ slug: string }>
}) {
  const { slug } = await params
  const product = getProduct(slug)
  if (!product) notFound()

  const related = PRODUCTS.filter(
    (p) => p.slug !== product.slug && p.color === product.color,
  )
    .concat(PRODUCTS.filter((p) => p.slug !== product.slug))
    .filter((p, i, arr) => arr.findIndex((x) => x.slug === p.slug) === i)
    .slice(0, 3)

  return (
    <div className="pt-6">
      {/* breadcrumb */}
      <nav
        aria-label="Breadcrumb"
        className="mx-auto mb-2 flex max-w-7xl items-center gap-1.5 px-4 text-sm text-muted-foreground sm:px-6 lg:px-8"
      >
        <Link href="/" className="hover:text-foreground">
          Home
        </Link>
        <ChevronRight className="size-3.5" />
        <Link href="/shop" className="hover:text-foreground">
          Shop
        </Link>
        <ChevronRight className="size-3.5" />
        <span className="text-foreground">{product.name}</span>
      </nav>

      <ProductDetail product={product} />

      {/* cross-sell */}
      <section className="mx-auto max-w-7xl px-4 pb-24 sm:px-6 lg:px-8">
        <h2 className="mb-8 font-serif text-3xl tracking-tight">
          You might also love
        </h2>
        <div className="grid grid-cols-2 gap-x-4 gap-y-10 lg:grid-cols-3">
          {related.map((p, i) => (
            <ProductCard key={p.slug} product={p} index={i} />
          ))}
        </div>
      </section>
    </div>
  )
}
