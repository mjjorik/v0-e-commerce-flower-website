'use client'

import Link from 'next/link'
import Image from 'next/image'
import { AnimatePresence, motion } from 'motion/react'
import { X, Plus, Minus, ShoppingBag } from 'lucide-react'
import {
  useCart,
  FREE_DELIVERY_THRESHOLD,
} from '@/components/cart/cart-provider'
import { ADD_ONS, formatPrice } from '@/lib/products'

export function CartDrawer() {
  const {
    items,
    isOpen,
    closeCart,
    updateQuantity,
    removeItem,
    subtotal,
    addItem,
  } = useCart()

  const remaining = Math.max(0, FREE_DELIVERY_THRESHOLD - subtotal)
  const progress = Math.min(100, (subtotal / FREE_DELIVERY_THRESHOLD) * 100)

  const suggestions = ADD_ONS.filter(
    (a) => !items.some((i) => i.id === `${a.slug}-one-addon`),
  ).slice(0, 2)

  return (
    <AnimatePresence>
      {isOpen && (
        <>
          <motion.div
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            exit={{ opacity: 0 }}
            onClick={closeCart}
            className="fixed inset-0 z-50 bg-foreground/30 backdrop-blur-sm"
          />
          <motion.aside
            initial={{ x: '100%' }}
            animate={{ x: 0 }}
            exit={{ x: '100%' }}
            transition={{ type: 'spring', stiffness: 320, damping: 36 }}
            className="fixed right-0 top-0 z-50 flex h-full w-full flex-col bg-background sm:max-w-md"
            aria-label="Shopping basket"
          >
            <div className="flex items-center justify-between border-b border-border px-5 py-4">
              <h2 className="font-serif text-xl">
                Your Basket{' '}
                <span className="text-muted-foreground">({items.length})</span>
              </h2>
              <button onClick={closeCart} aria-label="Close basket">
                <X className="size-5" />
              </button>
            </div>

            {/* free delivery progress */}
            <div className="border-b border-border px-5 py-4">
              <p className="text-sm">
                {remaining > 0 ? (
                  <>
                    You&apos;re{' '}
                    <span className="font-medium text-terracotta">
                      ${remaining}
                    </span>{' '}
                    away from free delivery
                  </>
                ) : (
                  <span className="font-medium text-primary">
                    You&apos;ve unlocked free delivery
                  </span>
                )}
              </p>
              <div className="mt-2 h-1.5 overflow-hidden rounded-full bg-muted">
                <motion.div
                  className="h-full rounded-full bg-primary"
                  initial={false}
                  animate={{ width: `${progress}%` }}
                  transition={{ type: 'spring', stiffness: 200, damping: 30 }}
                />
              </div>
            </div>

            {items.length === 0 ? (
              <div className="flex flex-1 flex-col items-center justify-center gap-4 px-6 text-center">
                <ShoppingBag
                  className="size-10 text-muted-foreground"
                  strokeWidth={1.2}
                />
                <p className="text-muted-foreground">
                  Your basket is empty — let&apos;s fix that.
                </p>
                <Link
                  href="/shop"
                  onClick={closeCart}
                  className="rounded-full bg-primary px-6 py-3 text-sm text-primary-foreground"
                >
                  Shop Bouquets
                </Link>
              </div>
            ) : (
              <>
                <div className="flex-1 overflow-y-auto px-5 py-4">
                  <ul className="space-y-5">
                    {items.map((item) => (
                      <li key={item.id} className="flex gap-4">
                        <div className="relative size-20 shrink-0 overflow-hidden rounded-md bg-card">
                          <Image
                            src={item.image || '/placeholder.svg'}
                            alt={item.imageAlt}
                            fill
                            sizes="80px"
                            className="object-cover"
                          />
                        </div>
                        <div className="flex flex-1 flex-col">
                          <div className="flex justify-between gap-2">
                            <div>
                              <p className="font-serif text-base leading-tight">
                                {item.name}
                              </p>
                              {!item.isAddOn && (
                                <p className="text-xs text-muted-foreground">
                                  {item.sizeLabel}
                                </p>
                              )}
                            </div>
                            <p className="text-sm">
                              {formatPrice(item.price * item.quantity)}
                            </p>
                          </div>
                          {item.giftMessage && (
                            <p className="mt-1 line-clamp-1 text-xs italic text-muted-foreground">
                              “{item.giftMessage}”
                            </p>
                          )}
                          <div className="mt-auto flex items-center justify-between pt-2">
                            <div className="flex items-center gap-3 rounded-full border border-border px-2 py-1">
                              <button
                                onClick={() =>
                                  updateQuantity(item.id, item.quantity - 1)
                                }
                                aria-label="Decrease quantity"
                              >
                                <Minus className="size-3.5" />
                              </button>
                              <span className="w-4 text-center text-sm">
                                {item.quantity}
                              </span>
                              <button
                                onClick={() =>
                                  updateQuantity(item.id, item.quantity + 1)
                                }
                                aria-label="Increase quantity"
                              >
                                <Plus className="size-3.5" />
                              </button>
                            </div>
                            <button
                              onClick={() => removeItem(item.id)}
                              className="text-xs text-muted-foreground underline-offset-2 hover:underline"
                            >
                              Remove
                            </button>
                          </div>
                        </div>
                      </li>
                    ))}
                  </ul>

                  {/* add-on suggestions */}
                  {suggestions.length > 0 && (
                    <div className="mt-8">
                      <p className="text-xs uppercase tracking-[0.2em] text-muted-foreground">
                        Add a little extra
                      </p>
                      <div className="mt-3 space-y-3">
                        {suggestions.map((a) => (
                          <div
                            key={a.slug}
                            className="flex items-center gap-3 rounded-lg bg-card p-2"
                          >
                            <div className="relative size-12 shrink-0 overflow-hidden rounded-md">
                              <Image
                                src={a.image || '/placeholder.svg'}
                                alt={a.imageAlt}
                                fill
                                sizes="48px"
                                className="object-cover"
                              />
                            </div>
                            <div className="flex-1">
                              <p className="text-sm leading-tight">{a.name}</p>
                              <p className="text-xs text-muted-foreground">
                                {formatPrice(a.price)}
                              </p>
                            </div>
                            <button
                              onClick={() =>
                                addItem({
                                  productSlug: a.slug,
                                  name: a.name,
                                  sizeKey: 'one',
                                  sizeLabel: 'Add-on',
                                  price: a.price,
                                  image: a.image,
                                  imageAlt: a.imageAlt,
                                  isAddOn: true,
                                })
                              }
                              className="rounded-full border border-primary px-3 py-1.5 text-xs text-primary transition-colors hover:bg-primary hover:text-primary-foreground"
                            >
                              Add
                            </button>
                          </div>
                        ))}
                      </div>
                    </div>
                  )}
                </div>

                <div className="border-t border-border px-5 py-4">
                  <div className="flex items-center justify-between text-base">
                    <span>Subtotal</span>
                    <span className="font-medium">{formatPrice(subtotal)}</span>
                  </div>
                  <p className="mt-1 text-xs text-muted-foreground">
                    Delivery &amp; taxes calculated at checkout.
                  </p>
                  <Link
                    href="/checkout"
                    onClick={closeCart}
                    className="mt-4 flex w-full items-center justify-center rounded-full bg-primary px-6 py-3.5 text-sm text-primary-foreground transition-transform hover:-translate-y-0.5"
                  >
                    Checkout
                  </Link>
                  <button
                    onClick={closeCart}
                    className="mt-2 w-full text-center text-sm text-muted-foreground underline-offset-2 hover:underline"
                  >
                    Continue shopping
                  </button>
                </div>
              </>
            )}
          </motion.aside>
        </>
      )}
    </AnimatePresence>
  )
}
