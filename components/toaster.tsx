'use client'

import { AnimatePresence, motion } from 'motion/react'
import { Check } from 'lucide-react'
import { useCart } from '@/components/cart/cart-provider'

export function Toaster() {
  const { toasts } = useCart()
  return (
    <div className="pointer-events-none fixed bottom-4 left-1/2 z-[60] flex w-full max-w-sm -translate-x-1/2 flex-col items-center gap-2 px-4">
      <AnimatePresence>
        {toasts.map((t) => (
          <motion.div
            key={t.id}
            initial={{ opacity: 0, y: 24, scale: 0.96 }}
            animate={{ opacity: 1, y: 0, scale: 1 }}
            exit={{ opacity: 0, y: 12, scale: 0.96 }}
            transition={{ type: 'spring', stiffness: 400, damping: 30 }}
            className="pointer-events-auto flex items-center gap-2 rounded-full bg-primary px-4 py-2.5 text-sm text-primary-foreground shadow-lg"
          >
            <span className="flex size-5 items-center justify-center rounded-full bg-primary-foreground/20">
              <Check className="size-3" />
            </span>
            {t.text}
          </motion.div>
        ))}
      </AnimatePresence>
    </div>
  )
}
