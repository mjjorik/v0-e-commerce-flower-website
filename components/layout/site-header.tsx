'use client'

import { useState, useEffect } from 'react'
import Link from 'next/link'
import { usePathname } from 'next/navigation'
import { AnimatePresence, motion, useReducedMotion } from 'motion/react'
import { Menu, X, ShoppingBag } from 'lucide-react'
import { useCart } from '@/components/cart/cart-provider'
import { BRAND } from '@/lib/brand'
import { cn } from '@/lib/utils'

const NAV = [
  { href: '/shop', label: 'Shop' },
  { href: '/subscriptions', label: 'Subscriptions' },
  { href: '/occasions', label: 'Occasions' },
  { href: '/delivery', label: 'Delivery' },
  { href: '/about', label: 'About' },
]

export function SiteHeader() {
  const [announce, setAnnounce] = useState(true)
  const [menuOpen, setMenuOpen] = useState(false)
  const [scrolled, setScrolled] = useState(false)
  const { count, openCart } = useCart()
  const pathname = usePathname()
  const reduce = useReducedMotion()

  useEffect(() => {
    const onScroll = () => setScrolled(window.scrollY > 24)
    onScroll()
    window.addEventListener('scroll', onScroll, { passive: true })
    return () => window.removeEventListener('scroll', onScroll)
  }, [])

  useEffect(() => {
    setMenuOpen(false)
  }, [pathname])

  useEffect(() => {
    document.body.style.overflow = menuOpen ? 'hidden' : ''
    return () => {
      document.body.style.overflow = ''
    }
  }, [menuOpen])

  return (
    <>
      <AnimatePresence>
        {announce && (
          <motion.div
            initial={false}
            exit={{ height: 0, opacity: 0 }}
            className="relative z-50 bg-primary text-primary-foreground"
          >
            <div className="mx-auto flex max-w-7xl items-center justify-center gap-3 px-4 py-2 text-center text-xs tracking-wide sm:text-[13px]">
              <p className="text-pretty">
                Same-day delivery across {BRAND.city} — order by {BRAND.cutoff}
              </p>
              <button
                onClick={() => setAnnounce(false)}
                aria-label="Dismiss announcement"
                className="absolute right-3 rounded-full p-1 transition-colors hover:bg-primary-foreground/15"
              >
                <X className="size-3.5" />
              </button>
            </div>
          </motion.div>
        )}
      </AnimatePresence>

      <header
        className={cn(
          'sticky top-0 z-40 transition-all duration-300',
          scrolled
            ? 'border-b border-border/60 bg-background/80 backdrop-blur-md'
            : 'bg-transparent',
        )}
      >
        <div className="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
          {/* left: hamburger (mobile) + nav (desktop) */}
          <div className="flex flex-1 items-center">
            <button
              className="lg:hidden"
              onClick={() => setMenuOpen(true)}
              aria-label="Open menu"
            >
              <Menu className="size-6" />
            </button>
            <nav className="hidden items-center gap-7 lg:flex">
              {NAV.map((item) => (
                <Link
                  key={item.href}
                  href={item.href}
                  className={cn(
                    'group relative text-sm tracking-wide text-foreground/80 transition-colors hover:text-foreground',
                    pathname.startsWith(item.href) && 'text-foreground',
                  )}
                >
                  {item.label}
                  <span className="absolute -bottom-1 left-0 h-px w-0 bg-foreground transition-all duration-300 group-hover:w-full" />
                </Link>
              ))}
            </nav>
          </div>

          {/* center: logo */}
          <Link
            href="/"
            className="flex-1 text-center font-serif text-2xl font-semibold tracking-tight sm:text-[26px]"
          >
            {BRAND.name}
          </Link>

          {/* right: order now + cart */}
          <div className="flex flex-1 items-center justify-end gap-3 sm:gap-4">
            <Link
              href="/shop"
              className="hidden rounded-full bg-primary px-5 py-2.5 text-sm text-primary-foreground transition-transform hover:-translate-y-0.5 lg:inline-flex"
            >
              Order Now
            </Link>
            <button
              onClick={openCart}
              aria-label={`Open basket, ${count} items`}
              className="relative"
            >
              <ShoppingBag className="size-6" strokeWidth={1.6} />
              <AnimatePresence>
                {count > 0 && (
                  <motion.span
                    key={count}
                    initial={reduce ? false : { scale: 0 }}
                    animate={{ scale: 1 }}
                    exit={{ scale: 0 }}
                    transition={{ type: 'spring', stiffness: 500, damping: 18 }}
                    className="absolute -right-2 -top-2 flex size-5 items-center justify-center rounded-full bg-terracotta text-[11px] font-medium text-terracotta-foreground"
                  >
                    {count}
                  </motion.span>
                )}
              </AnimatePresence>
            </button>
          </div>
        </div>
      </header>

      {/* full-screen mobile overlay */}
      <AnimatePresence>
        {menuOpen && (
          <motion.div
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            exit={{ opacity: 0 }}
            transition={{ duration: 0.3 }}
            className="fixed inset-0 z-50 flex flex-col bg-card"
          >
            <div className="flex items-center justify-between px-4 py-4 sm:px-6">
              <span className="font-serif text-2xl font-semibold">
                {BRAND.name}
              </span>
              <button onClick={() => setMenuOpen(false)} aria-label="Close menu">
                <X className="size-6" />
              </button>
            </div>

            <nav className="flex flex-1 flex-col justify-center gap-2 px-6">
              {[{ href: '/', label: 'Home' }, ...NAV].map((item, i) => (
                <motion.div
                  key={item.href}
                  initial={{ opacity: 0, y: 20 }}
                  animate={{ opacity: 1, y: 0 }}
                  transition={{ delay: 0.1 + i * 0.06 }}
                >
                  <Link
                    href={item.href}
                    className="block py-2 font-serif text-4xl font-medium tracking-tight sm:text-5xl"
                  >
                    {item.label}
                  </Link>
                </motion.div>
              ))}
            </nav>

            <div className="border-t border-border px-6 py-6 text-sm text-muted-foreground">
              <p className="mb-1">{BRAND.email}</p>
              <p>
                Same-day across {BRAND.city} · order by {BRAND.cutoff}
              </p>
            </div>
          </motion.div>
        )}
      </AnimatePresence>
    </>
  )
}
