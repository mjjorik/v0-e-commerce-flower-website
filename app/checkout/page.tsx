'use client'

import { useState } from 'react'
import Link from 'next/link'
import { motion, AnimatePresence } from 'motion/react'
import { ChevronLeft, CheckCircle2 } from 'lucide-react'
import { useCart } from '@/components/cart/cart-provider'
import { formatPrice } from '@/lib/products'
import { cn } from '@/lib/utils'

type Step = 'delivery' | 'details' | 'payment' | 'success'

export default function CheckoutClient() {
  const { items, subtotal, clearCart } = useCart()
  const [step, setStep] = useState<Step>('delivery')

  const deliveryFee = 15
  const taxes = subtotal * 0.0625 // MA tax
  const total = subtotal + deliveryFee + taxes

  if (items.length === 0 && step !== 'success') {
    return (
      <div className="mx-auto flex max-w-md flex-col items-center justify-center py-32 text-center px-4">
        <h1 className="font-serif text-3xl">Your basket is empty</h1>
        <p className="mt-2 text-muted-foreground">You cannot checkout without items.</p>
        <Link href="/shop" className="btn-primary mt-8">
          Go to Shop
        </Link>
      </div>
    )
  }

  return (
    <div className="mx-auto max-w-3xl px-4 py-12 sm:px-6 lg:px-8">
      {step !== 'success' && (
        <div className="mb-10 flex items-center justify-between border-b border-border pb-4">
          <Link href="/shop" className="flex items-center gap-1 text-sm text-muted-foreground hover:text-foreground">
            <ChevronLeft className="size-4" /> Back to shop
          </Link>
          <div className="flex items-center gap-2 text-sm font-medium uppercase tracking-widest text-muted-foreground">
            <span className={cn(step === 'delivery' && 'text-foreground')}>1. Delivery</span>
            <span className="opacity-40">/</span>
            <span className={cn(step === 'details' && 'text-foreground')}>2. Details</span>
            <span className="opacity-40">/</span>
            <span className={cn(step === 'payment' && 'text-foreground')}>3. Payment</span>
          </div>
        </div>
      )}

      <AnimatePresence mode="wait">
        {step === 'delivery' && (
          <motion.div key="delivery" initial={{ opacity: 0, x: 20 }} animate={{ opacity: 1, x: 0 }} exit={{ opacity: 0, x: -20 }} className="space-y-8">
            <h1 className="font-serif text-3xl">Where are we going?</h1>
            <div className="grid gap-4 sm:grid-cols-2">
              <input type="text" placeholder="Recipient First Name" className="w-full rounded-xl border border-border bg-transparent px-4 py-3 outline-none focus:border-foreground" />
              <input type="text" placeholder="Recipient Last Name" className="w-full rounded-xl border border-border bg-transparent px-4 py-3 outline-none focus:border-foreground" />
              <input type="text" placeholder="Street Address" className="sm:col-span-2 w-full rounded-xl border border-border bg-transparent px-4 py-3 outline-none focus:border-foreground" />
              <input type="text" placeholder="Apt, Suite, etc (optional)" className="sm:col-span-2 w-full rounded-xl border border-border bg-transparent px-4 py-3 outline-none focus:border-foreground" />
              <input type="text" placeholder="City" defaultValue="Boston" className="w-full rounded-xl border border-border bg-transparent px-4 py-3 outline-none focus:border-foreground" />
              <input type="text" placeholder="Zip Code" className="w-full rounded-xl border border-border bg-transparent px-4 py-3 outline-none focus:border-foreground" />
            </div>
            <button onClick={() => setStep('details')} className="btn-primary w-full">
              Continue to Details
            </button>
          </motion.div>
        )}

        {step === 'details' && (
          <motion.div key="details" initial={{ opacity: 0, x: 20 }} animate={{ opacity: 1, x: 0 }} exit={{ opacity: 0, x: -20 }} className="space-y-8">
            <h1 className="font-serif text-3xl">Your Information</h1>
            <div className="grid gap-4 sm:grid-cols-2">
              <input type="email" placeholder="Your Email (for receipt)" className="sm:col-span-2 w-full rounded-xl border border-border bg-transparent px-4 py-3 outline-none focus:border-foreground" />
              <input type="tel" placeholder="Your Phone Number" className="sm:col-span-2 w-full rounded-xl border border-border bg-transparent px-4 py-3 outline-none focus:border-foreground" />
            </div>
            <button onClick={() => setStep('payment')} className="btn-primary w-full">
              Continue to Payment
            </button>
            <button onClick={() => setStep('delivery')} className="w-full text-center text-sm text-muted-foreground hover:underline">
              Go back
            </button>
          </motion.div>
        )}

        {step === 'payment' && (
          <motion.div key="payment" initial={{ opacity: 0, x: 20 }} animate={{ opacity: 1, x: 0 }} exit={{ opacity: 0, x: -20 }} className="space-y-8">
            <h1 className="font-serif text-3xl">Payment</h1>
            
            {/* Order Summary */}
            <div className="rounded-2xl bg-card p-6">
              <h3 className="mb-4 font-serif text-xl">Order Summary</h3>
              <div className="space-y-2 text-sm">
                <div className="flex justify-between"><span>Subtotal</span><span>{formatPrice(subtotal)}</span></div>
                <div className="flex justify-between"><span>Delivery</span><span>{formatPrice(deliveryFee)}</span></div>
                <div className="flex justify-between"><span>Taxes</span><span>{formatPrice(taxes)}</span></div>
                <div className="mt-4 flex justify-between border-t border-border pt-4 font-serif text-xl">
                  <span>Total</span><span>{formatPrice(total)}</span>
                </div>
              </div>
            </div>

            <div className="rounded-2xl border border-border p-6 text-center">
              <p className="text-muted-foreground text-sm">Stripe Payment Element will be mounted here.</p>
              <div className="mt-4 h-12 w-full rounded bg-muted animate-pulse"></div>
            </div>

            <button onClick={() => { clearCart(); setStep('success') }} className="btn-primary w-full">
              Pay {formatPrice(total)}
            </button>
            <button onClick={() => setStep('details')} className="w-full text-center text-sm text-muted-foreground hover:underline">
              Go back
            </button>
          </motion.div>
        )}

        {step === 'success' && (
          <motion.div key="success" initial={{ opacity: 0, scale: 0.95 }} animate={{ opacity: 1, scale: 1 }} className="flex flex-col items-center justify-center py-20 text-center">
            <CheckCircle2 className="size-16 text-sage mb-6" />
            <h1 className="font-serif text-4xl mb-4">Order Confirmed</h1>
            <p className="text-muted-foreground max-w-md">Thank you for your order. We've sent a receipt to your email. We'll text you when the flowers are out for delivery.</p>
            <Link href="/" className="btn-outline mt-10">
              Return Home
            </Link>
          </motion.div>
        )}
      </AnimatePresence>
    </div>
  )
}
