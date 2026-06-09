import { Suspense } from 'react'
import type { Metadata } from 'next'
import { PageHeader } from '@/components/page-header'
import { ShopClient } from '@/components/shop/shop-client'

export const metadata: Metadata = {
  title: 'Shop Bouquets',
  description:
    'Browse farm-fresh bouquets from $50 to $130. Filter by price, color, occasion and same-day availability across Greater Boston.',
}

export default function ShopPage() {
  return (
    <>
      <PageHeader
        eyebrow="The shop"
        title="Every bouquet, no markups"
        intro="Cut this week, priced honestly. Filter your way to the right bunch, then send it same-day."
      />
      <Suspense fallback={null}>
        <ShopClient />
      </Suspense>
    </>
  )
}
