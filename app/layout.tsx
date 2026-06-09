import { Analytics } from '@vercel/analytics/next'
import type { Metadata } from 'next'
import { Fraunces, Inter } from 'next/font/google'
import './globals.css'
import { CartProvider } from '@/components/cart/cart-provider'
import { SiteHeader } from '@/components/layout/site-header'
import { SiteFooter } from '@/components/layout/site-footer'
import { CartDrawer } from '@/components/cart/cart-drawer'
import { Toaster } from '@/components/toaster'
import { BRAND } from '@/lib/brand'

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
  title: {
    default: `${BRAND.name} — Fresh flowers, honestly priced. Greater Boston.`,
    template: `%s · ${BRAND.name}`,
  },
  description:
    'Farm-fresh bouquets and weekly flower subscriptions delivered same-day across Greater Boston. Beautiful flowers, honest prices — $50 to $130.',
  generator: 'v0.app',
}

export const viewport = {
  themeColor: '#FAF7F2',
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
