import type { MetadataRoute } from 'next'
import { BRAND } from '@/lib/brand'

export const dynamic = 'force-static'

export default function manifest(): MetadataRoute.Manifest {
  return {
    name: `${BRAND.name} — Fresh flowers, honestly priced`,
    short_name: BRAND.name,
    description:
      'Farm-fresh bouquets and weekly flower subscriptions, delivered same-day across Greater Boston.',
    start_url: '/',
    display: 'standalone',
    background_color: '#FDFBF7',
    theme_color: '#FDFBF7',
    icons: [
      { src: '/icon.svg', sizes: 'any', type: 'image/svg+xml' },
      { src: '/apple-icon.png', sizes: '180x180', type: 'image/png' },
    ],
  }
}
