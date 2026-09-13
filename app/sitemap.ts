import type { MetadataRoute } from 'next'
import { PRODUCTS } from '@/lib/products'
import { abs } from '@/lib/seo'

export const dynamic = 'force-static'

export default function sitemap(): MetadataRoute.Sitemap {
  const now = new Date()

  const staticRoutes = [
    { path: '/', priority: 1, freq: 'daily' as const },
    { path: '/shop', priority: 0.9, freq: 'daily' as const },
    { path: '/subscriptions', priority: 0.8, freq: 'weekly' as const },
    { path: '/occasions', priority: 0.7, freq: 'weekly' as const },
    { path: '/delivery', priority: 0.6, freq: 'monthly' as const },
    { path: '/about', priority: 0.5, freq: 'monthly' as const },
    { path: '/contact', priority: 0.5, freq: 'monthly' as const },
  ].map((r) => ({
    url: abs(r.path),
    lastModified: now,
    changeFrequency: r.freq,
    priority: r.priority,
  }))

  const productRoutes = PRODUCTS.map((p) => ({
    url: abs(`/shop/${p.slug}`),
    lastModified: now,
    changeFrequency: 'weekly' as const,
    priority: 0.8,
  }))

  return [...staticRoutes, ...productRoutes]
}
