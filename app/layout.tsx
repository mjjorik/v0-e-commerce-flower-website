import { Analytics } from '@vercel/analytics/next'
import type { Metadata } from 'next'
import { Fraunces, Inter } from 'next/font/google'
import './globals.css'
import { CartProvider } from '@/components/cart/cart-provider'
import { SiteHeader } from '@/components/layout/site-header'
import { SiteFooter } from '@/components/layout/site-footer'
import { CartDrawer } from '@/components/cart/cart-drawer'
import { Toaster } from '@/components/toaster'
import { JsonLd } from '@/components/json-ld'
import { BRAND } from '@/lib/brand'
import { localBusinessLd, webSiteLd } from '@/lib/seo'

const fraunces = Fraunces({
  variable: '--font-fraunces',
  subsets: ['latin'],
  display: 'swap',
})
const inter = Inter({
  variable: '--font-inter',
  subsets: ['latin'],
  display: 'swap',
})

export const metadata: Metadata = {
  metadataBase: new URL(BRAND.url),
  title: {
    default: `${BRAND.name} — Fresh flowers, honestly priced. Greater Boston.`,
    template: `%s · ${BRAND.name}`,
  },
  description:
    'Farm-fresh bouquets and weekly flower subscriptions delivered same-day across Greater Boston. Beautiful flowers, honest prices — $50 to $130.',
  applicationName: BRAND.name,
  generator: 'v0.app',
  keywords: [
    'flower delivery Boston',
    'same-day flower delivery',
    'Boston florist',
    'flower subscription Boston',
    'fresh bouquets Cambridge',
    'Somerville flower delivery',
  ],
  authors: [{ name: BRAND.name }],
  creator: BRAND.name,
  publisher: BRAND.name,
  alternates: { canonical: '/' },
  category: 'Florist',
  openGraph: {
    type: 'website',
    locale: 'en_US',
    url: BRAND.url,
    siteName: BRAND.name,
    title: `${BRAND.name} — Fresh flowers, honestly priced. Greater Boston.`,
    description:
      'Farm-fresh bouquets and weekly subscriptions, delivered same-day across Greater Boston. Honest prices from $50.',
    images: [
      {
        url: '/hero/hero-bouquet.png',
        width: 1200,
        height: 1600,
        alt: 'A hand-tied Wildflower bouquet from Boston',
      },
    ],
  },
  twitter: {
    card: 'summary_large_image',
    title: `${BRAND.name} — Fresh flowers, honestly priced.`,
    description:
      'Same-day flower delivery across Greater Boston. Honest prices from $50.',
    images: ['/hero/hero-bouquet.png'],
  },
  robots: {
    index: true,
    follow: true,
    googleBot: {
      index: true,
      follow: true,
      'max-image-preview': 'large',
      'max-snippet': -1,
      'max-video-preview': -1,
    },
  },
}

export const viewport = {
  themeColor: '#FDFBF7',
}

export default function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode
}>) {
  return (
    <html
      lang="en"
      className={`${fraunces.variable} ${inter.variable} bg-background`}
    >
      <body className="font-sans antialiased">
        <JsonLd data={[localBusinessLd(), webSiteLd()]} />
        <CartProvider>
          <SiteHeader />
          <main>{children}</main>
          <SiteFooter />
          <CartDrawer />
          <Toaster />
        </CartProvider>
        {process.env.NODE_ENV === 'production' && <Analytics />}
      </body>
    </html>
  )
}
