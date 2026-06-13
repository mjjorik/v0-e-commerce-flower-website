'use client'

import { useState } from 'react'
import Link from 'next/link'
import { motion, AnimatePresence } from 'motion/react'
import { ChevronLeft, CheckCircle2, ShieldCheck, Sprout, Lock, Check } from 'lucide-react'
import { useCart } from '@/components/cart/cart-provider'
import { SmartImage } from '@/components/smart-image'
import { formatPrice } from '@/lib/products'
import { cn } from '@/lib/utils'

type Step = 'delivery' | 'details' | 'payment' | 'success'

const STEPS: { id: Step; label: string }[] = [
  { id: 'delivery', label: 'Delivery' },
  { id: 'details', label: 'Details' },
  { id: 'payment', label: 'Payment' },
]

const inputCls =
  'w-full rounded-[var(--radius-md)] border border-border bg-card/40 px-4 py-3 text-sm outline-none transition-colors placeholder:text-muted-foreground focus:border-foreground focus:bg-card'

export default function CheckoutClient() {
  const { items, subtotal, clearCart } = useCart()
  const [step, setStep] = useState<Step>('delivery')

  const deliveryFee = 15
  const taxes = subtotal * 0.0625 // MA tax
  const total = subtotal + deliveryFee + taxes
  const currentIndex = STEPS.findIndex((s) => s.id === step)

  if (items.length === 0 && step !== 'success') {
    return (
      <div className="mx-auto flex max-w-md flex-col items-center justify-center px-4 py-32 text-center">
        <h1 className="font-serif text-3xl">Your basket is empty</h1>
        <p className="mt-2 text-muted-foreground">
          Add a bouquet or two and they’ll show up right here.
        </p>
        <Link href="/shop" className="btn-primary mt-8">
          Go to Shop
        </Link>
      </div>
    )
  }

  if (step === 'success') {
    return (
      <div className="mx-auto max-w-3xl px-4 py-24 sm:px-6 lg:px-8">
        <motion.div
          initial={{ opacity: 0, scale: 0.96 }}
          animate={{ opacity: 1, scale: 1 }}
          transition={{ ease: [0.22, 1, 0.36, 1], duration: 0.5 }}
          className="flex flex-col items-center text-center"
        >
          <span className="flex size-16 items-center justify-center rounded-full bg-sage/50 text-primary">
            <CheckCircle2 className="size-9" strokeWidth={1.5} />
          </span>
          <h1 className="mt-6 font-serif text-4xl tracking-tight">Order confirmed</h1>
          <p className="mt-4 max-w-md text-pretty leading-relaxed text-foreground/65">
            Thank you — we’ve emailed your receipt. We’ll text you the moment your
            flowers are out for delivery across Greater Boston.
          </p>
          <Link href="/" className="btn-outline mt-10">
            Return home
          </Link>
        </motion.div>
      </div>
    )
  }

  return (
    <div className="mx-auto max-w-6xl px-4 py-12 sm:px-6 lg:px-8">
      {/* top bar */}
      <div className="mb-10 flex flex-col gap-6 border-b border-border pb-6 sm:flex-row sm:items-center sm:justify-between">
        <Link
          href="/shop"
          className="inline-flex items-center gap-1 text-sm text-muted-foreground transition-colors hover:text-foreground"
        >
          <ChevronLeft className="size-4" /> Back to shop
        </Link>

        {/* stepper */}
        <ol className="flex items-center gap-3 text-[13px]">
          {STEPS.map((s, i) => {
            const done = i < currentIndex
            const active = i === currentIndex
            return (
              <li key={s.id} className="flex items-center gap-3">
                <span className="flex items-center gap-2">
                  <span
                    className={cn(
                      'flex size-6 items-center justify-center rounded-full border text-[11px] font-medium transition-colors',
                      done && 'border-primary bg-primary text-primary-foreground',
                      active && 'border-foreground text-foreground',
                      !done && !active && 'border-border text-muted-foreground',
                    )}
                  >
                    {done ? <Check className="size-3.5" strokeWidth={3} /> : i + 1}
                  </span>
                  <span
                    className={cn(
                      'tracking-tight transition-colors',
                      active ? 'text-foreground' : 'text-muted-foreground',
                    )}
                  >
                    {s.label}
                  </span>
                </span>
                {i < STEPS.length - 1 && <span className="h-px w-6 bg-border" />}
              </li>
            )
          })}
        </ol>
      </div>

      <div className="grid gap-12 lg:grid-cols-[1fr_22rem]">
        {/* forms */}
        <div>
          <AnimatePresence mode="wait">
            {step === 'delivery' && (
              <motion.div
                key="delivery"
                initial={{ opacity: 0, x: 16 }}
                animate={{ opacity: 1, x: 0 }}
                exit={{ opacity: 0, x: -16 }}
                transition={{ duration: 0.3, ease: [0.22, 1, 0.36, 1] }}
                className="space-y-6"
              >
                <h1 className="font-serif text-3xl tracking-tight">Where are we going?</h1>
                <div className="grid gap-4 sm:grid-cols-2">
                  <input type="text" placeholder="Recipient first name" className={inputCls} />
                  <input type="text" placeholder="Recipient last name" className={inputCls} />
                  <input type="text" placeholder="Street address" className={cn(inputCls, 'sm:col-span-2')} />
                  <input type="text" placeholder="Apt, suite, etc. (optional)" className={cn(inputCls, 'sm:col-span-2')} />
                  <input type="text" placeholder="City" defaultValue="Boston" className={inputCls} />
                  <input type="text" placeholder="Zip code" className={inputCls} />
                </div>
                <button onClick={() => setStep('details')} className="btn-primary w-full">
                  Continue to details
                </button>
              </motion.div>
            )}

            {step === 'details' && (
              <motion.div
                key="details"
                initial={{ opacity: 0, x: 16 }}
                animate={{ opacity: 1, x: 0 }}
                exit={{ opacity: 0, x: -16 }}
                transition={{ duration: 0.3, ease: [0.22, 1, 0.36, 1] }}
                className="space-y-6"
              >
                <h1 className="font-serif text-3xl tracking-tight">Your information</h1>
                <div className="grid gap-4">
                  <input type="email" placeholder="Your email (for the receipt)" className={inputCls} />
                  <input type="tel" placeholder="Your phone number" className={inputCls} />
                </div>
                <p className="text-sm text-muted-foreground">
                  We’ll only use these to send your receipt and delivery updates.
                </p>
                <button onClick={() => setStep('payment')} className="btn-primary w-full">
                  Continue to payment
                </button>
                <button
                  onClick={() => setStep('delivery')}
                  className="w-full text-center text-sm text-muted-foreground underline-offset-4 hover:underline"
                >
                  Go back
                </button>
              </motion.div>
            )}

            {step === 'payment' && (
              <motion.div
                key="payment"
                initial={{ opacity: 0, x: 16 }}
                animate={{ opacity: 1, x: 0 }}
                exit={{ opacity: 0, x: -16 }}
                transition={{ duration: 0.3, ease: [0.22, 1, 0.36, 1] }}
                className="space-y-6"
              >
                <h1 className="font-serif text-3xl tracking-tight">Payment</h1>
                <div className="rounded-2xl border border-border bg-card/40 p-6">
                  <div className="flex items-center gap-2 text-sm text-muted-foreground">
                    <Lock className="size-4" />
                    Secure card payment
                  </div>
                  <div className="mt-4 h-12 w-full animate-pulse rounded-[var(--radius-md)] bg-muted" />
                  <p className="mt-3 text-xs text-muted-foreground">
                    The Stripe Payment Element mounts here in production.
                  </p>
                </div>
                <button
                  onClick={() => {
                    clearCart()
                    setStep('success')
                  }}
                  className="btn-primary w-full"
                >
                  Pay {formatPrice(total)}
                </button>
                <button
                  onClick={() => setStep('details')}
                  className="w-full text-center text-sm text-muted-foreground underline-offset-4 hover:underline"
                >
                  Go back
                </button>
              </motion.div>
            )}
          </AnimatePresence>
        </div>

        {/* order summary */}
        <aside className="lg:sticky lg:top-28 lg:self-start">
          <div className="rounded-2xl border border-border bg-card/50 p-6">
            <h2 className="font-serif text-xl">Order summary</h2>

            <ul className="mt-5 space-y-4">
              {items.map((item) => (
                <li key={item.id} className="flex gap-3">
                  <div className="relative size-14 shrink-0 overflow-hidden rounded-[var(--radius-md)] bg-card">
                    <SmartImage
                      src={item.image}
                      alt={item.imageAlt}
                      fill
                      sizes="56px"
                      showMotif={false}
                      className="object-cover"
                    />
                    <span className="absolute -right-1.5 -top-1.5 flex size-5 items-center justify-center rounded-full bg-primary text-[11px] font-medium text-primary-foreground">
                      {item.quantity}
                    </span>
                  </div>
                  <div className="flex flex-1 items-start justify-between gap-2">
                    <div>
                      <p className="text-sm leading-tight">{item.name}</p>
                      {!item.isAddOn && (
                        <p className="text-xs text-muted-foreground">{item.sizeLabel}</p>
                      )}
                    </div>
                    <p className="text-sm">{formatPrice(item.price * item.quantity)}</p>
                  </div>
                </li>
              ))}
            </ul>

            <div className="mt-6 space-y-2 border-t border-border pt-4 text-sm">
              <div className="flex justify-between text-foreground/70">
                <span>Subtotal</span>
                <span>{formatPrice(subtotal)}</span>
              </div>
              <div className="flex justify-between text-foreground/70">
                <span>Delivery</span>
                <span>{formatPrice(deliveryFee)}</span>
              </div>
              <div className="flex justify-between text-foreground/70">
                <span>Taxes (MA 6.25%)</span>
                <span>{formatPrice(taxes)}</span>
              </div>
              <div className="flex justify-between border-t border-border pt-3 font-serif text-lg">
                <span>Total</span>
                <span>{formatPrice(total)}</span>
              </div>
            </div>
          </div>

          {/* trust */}
          <ul className="mt-5 space-y-2.5 px-1 text-sm text-muted-foreground">
            <li className="flex items-center gap-2.5">
              <ShieldCheck className="size-4 text-primary" /> Secure, encrypted checkout
            </li>
            <li className="flex items-center gap-2.5">
              <Sprout className="size-4 text-primary" /> 7-day freshness guarantee
            </li>
          </ul>
        </aside>
      </div>
    </div>
  )
}
