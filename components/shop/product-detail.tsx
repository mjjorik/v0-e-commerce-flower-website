'use client'

import { useState } from 'react'
import { motion, AnimatePresence } from 'motion/react'
import { ChevronDown, Check } from 'lucide-react'
import { SmartImage } from '@/components/smart-image'
import { useCart } from '@/components/cart/cart-provider'
import type { Product } from '@/lib/products'
import { formatPrice } from '@/lib/products'
import { cn } from '@/lib/utils'

const TIME_SLOTS = ['9 AM – 12 PM', '12 – 3 PM', '3 – 6 PM', '6 – 9 PM']

function nextDays(count: number) {
  const days = []
  const now = new Date()
  for (let i = 0; i < count; i++) {
    const d = new Date(now)
    d.setDate(now.getDate() + i)
    days.push(d)
  }
  return days
}

export function ProductDetail({ product }: { product: Product }) {
  const { addItem } = useCart()
  const gallery = [product.image, product.hoverImage]
  const [activeImg, setActiveImg] = useState(0)
  const [size, setSize] = useState(product.sizes[1] ?? product.sizes[0])
  const [dateIdx, setDateIdx] = useState(0)
  const [slot, setSlot] = useState(TIME_SLOTS[1])
  const [message, setMessage] = useState('')
  const [open, setOpen] = useState<string | null>('inside')

  const days = nextDays(8)
  const pastCutoff = new Date().getHours() >= 13
  const MAX = 200

  const handleAdd = () => {
    const d = days[dateIdx]
    addItem({
      productSlug: product.slug,
      name: product.name,
      sizeKey: size.key,
      sizeLabel: size.label,
      price: size.price,
      image: product.image,
      imageAlt: product.imageAlt,
      giftMessage: message || undefined,
      deliveryDate: `${d.toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: 'numeric' })}, ${slot}`,
    })
  }

  return (
    <div className="mx-auto max-w-7xl px-4 pb-24 sm:px-6 lg:px-8">
      <div className="grid gap-10 lg:grid-cols-2 lg:gap-16">
        {/* gallery */}
        <div className="lg:sticky lg:top-28 lg:self-start">
          <motion.div
            key={activeImg}
            initial={{ opacity: 0.4, scale: 1.03 }}
            animate={{ opacity: 1, scale: 1 }}
            transition={{ duration: 0.5 }}
            className="group relative aspect-[4/5] overflow-hidden rounded-2xl bg-card"
          >
            <SmartImage
              src={gallery[activeImg]}
              alt={product.imageAlt}
              fill
              priority
              sizes="(max-width: 1024px) 100vw, 50vw"
              fallbackLabel={product.name}
              className="object-cover transition-transform duration-700 group-hover:scale-105"
            />
          </motion.div>
          <div className="mt-3 flex gap-3">
            {gallery.map((src, i) => (
              <button
                key={i}
                onClick={() => setActiveImg(i)}
                className={cn(
                  'relative aspect-square w-20 overflow-hidden rounded-lg bg-card transition-all',
                  activeImg === i
                    ? 'ring-2 ring-primary ring-offset-2 ring-offset-background'
                    : 'opacity-70 hover:opacity-100',
                )}
                aria-label={`View image ${i + 1}`}
              >
                <SmartImage
                  src={src}
                  alt=""
                  fill
                  sizes="80px"
                  showMotif={false}
                  className="object-cover"
                />
              </button>
            ))}
          </div>
        </div>

        {/* info */}
        <div>
          <div className="flex flex-wrap items-center gap-2">
            {product.bestseller && (
              <span className="rounded-full bg-primary px-2.5 py-1 text-[11px] text-primary-foreground">
                Bestseller
              </span>
            )}
            {product.sameDay && (
              <span className="rounded-full bg-sage px-2.5 py-1 text-[11px] text-sage-foreground">
                Same-day eligible
              </span>
            )}
          </div>

          <h1 className="mt-3 font-serif text-4xl tracking-tight sm:text-5xl">
            {product.name}
          </h1>
          <p className="mt-2 font-serif text-2xl text-terracotta">
            {formatPrice(size.price)}
          </p>
          <p className="mt-4 max-w-md text-pretty leading-relaxed text-foreground/75">
            {product.description}
          </p>

          {/* size selector */}
          <div className="mt-8">
            <p className="mb-3 text-xs uppercase tracking-[0.2em] text-muted-foreground">
              Choose a size
            </p>
            <div className="grid grid-cols-3 gap-3">
              {product.sizes.map((s) => (
                <button
                  key={s.key}
                  onClick={() => setSize(s)}
                  className={cn(
                    'rounded-xl border p-3 text-left transition-all',
                    size.key === s.key
                      ? 'border-primary bg-primary/5'
                      : 'border-border hover:border-foreground',
                  )}
                >
                  <span className="block font-serif text-lg">{s.label}</span>
                  <span className="block text-sm text-foreground/70">
                    {formatPrice(s.price)}
                  </span>
                  <span className="mt-1 block text-xs text-muted-foreground">
                    {s.stems}
                  </span>
                </button>
              ))}
            </div>
          </div>

          {/* delivery date */}
          <div className="mt-8">
            <div className="mb-3 flex items-center justify-between">
              <p className="text-xs uppercase tracking-[0.2em] text-muted-foreground">
                Delivery date
              </p>
              {pastCutoff && dateIdx === 0 && (
                <p className="text-xs text-terracotta">
                  Today&apos;s 1 PM cutoff has passed
                </p>
              )}
            </div>
            <div className="no-scrollbar flex gap-2 overflow-x-auto pb-1">
              {days.map((d, i) => {
                const disabled = pastCutoff && i === 0
                const isToday = i === 0
                return (
                  <button
                    key={i}
                    disabled={disabled}
                    onClick={() => setDateIdx(i)}
                    className={cn(
                      'flex min-w-16 shrink-0 flex-col items-center rounded-xl border px-3 py-2.5 transition-all',
                      dateIdx === i
                        ? 'border-primary bg-primary text-primary-foreground'
                        : 'border-border hover:border-foreground',
                      disabled && 'cursor-not-allowed opacity-40',
                    )}
                  >
                    <span className="text-[11px] uppercase tracking-wide">
                      {isToday
                        ? 'Today'
                        : d.toLocaleDateString('en-US', { weekday: 'short' })}
                    </span>
                    <span className="font-serif text-lg">{d.getDate()}</span>
                  </button>
                )
              })}
            </div>
          </div>

          {/* time slot */}
          <div className="mt-6">
            <p className="mb-3 text-xs uppercase tracking-[0.2em] text-muted-foreground">
              Time slot
            </p>
            <div className="grid grid-cols-2 gap-2 sm:grid-cols-4">
              {TIME_SLOTS.map((s) => (
                <button
                  key={s}
                  onClick={() => setSlot(s)}
                  className={cn(
                    'rounded-lg border px-2 py-2.5 text-sm transition-all',
                    slot === s
                      ? 'border-primary bg-primary/5'
                      : 'border-border hover:border-foreground',
                  )}
                >
                  {s}
                </button>
              ))}
            </div>
          </div>

          {/* gift message */}
          <div className="mt-6">
            <p className="mb-3 text-xs uppercase tracking-[0.2em] text-muted-foreground">
              Gift message (optional)
            </p>
            <div className="rounded-xl border border-border focus-within:border-foreground">
              <textarea
                value={message}
                maxLength={MAX}
                onChange={(e) => setMessage(e.target.value)}
                rows={3}
                placeholder="Happy Tuesday — thought of you."
                className="w-full resize-none bg-transparent p-3 text-sm outline-none placeholder:text-muted-foreground"
              />
              <div className="px-3 pb-2 text-right text-xs text-muted-foreground">
                {message.length}/{MAX}
              </div>
            </div>
          </div>

          {/* add to cart */}
          <motion.button
            whileTap={{ scale: 0.98 }}
            onClick={handleAdd}
            className="btn-primary btn-lg mt-8 w-full"
          >
            Add to Basket · {formatPrice(size.price)}
          </motion.button>

          {/* accordions */}
          <div className="mt-10 divide-y divide-border border-y border-border">
            <Accordion
              id="inside"
              title="What's inside"
              open={open === 'inside'}
              onToggle={(id) => setOpen(open === id ? null : id)}
            >
              <ul className="space-y-1.5">
                {product.whatsInside.map((item) => (
                  <li
                    key={item}
                    className="flex items-center gap-2 text-foreground/75"
                  >
                    <Check className="size-4 text-primary" />
                    {item}
                  </li>
                ))}
              </ul>
            </Accordion>
            <Accordion
              id="care"
              title="Care tips"
              open={open === 'care'}
              onToggle={(id) => setOpen(open === id ? null : id)}
            >
              <ul className="list-disc space-y-1.5 pl-5 text-foreground/75">
                {product.careTips.map((tip) => (
                  <li key={tip}>{tip}</li>
                ))}
              </ul>
            </Accordion>
            <Accordion
              id="delivery"
              title="Delivery details"
              open={open === 'delivery'}
              onToggle={(id) => setOpen(open === id ? null : id)}
            >
              <p className="text-foreground/75">
                Hand-delivered across Greater Boston. Order by 1 PM for same-day
                delivery; otherwise choose any date above. You&apos;ll get a text
                when it&apos;s on the way.
              </p>
            </Accordion>
          </div>
        </div>
      </div>

      {/* sticky mobile bar */}
      <div className="fixed inset-x-0 bottom-0 z-30 flex items-center justify-between gap-3 border-t border-border bg-background/95 px-4 py-3 backdrop-blur lg:hidden">
        <div>
          <p className="font-serif text-lg leading-none">
            {formatPrice(size.price)}
          </p>
          <p className="text-xs text-muted-foreground">{size.label}</p>
        </div>
        <button onClick={handleAdd} className="btn-primary flex-1">
          Add to Basket
        </button>
      </div>
    </div>
  )
}

function Accordion({
  id,
  title,
  open,
  onToggle,
  children,
}: {
  id: string
  title: string
  open: boolean
  onToggle: (id: string) => void
  children: React.ReactNode
}) {
  return (
    <div>
      <button
        onClick={() => onToggle(id)}
        className="flex w-full items-center justify-between py-4 text-left"
        aria-expanded={open}
      >
        <span className="font-serif text-lg">{title}</span>
        <ChevronDown
          className={cn('size-5 transition-transform', open && 'rotate-180')}
        />
      </button>
      <AnimatePresence initial={false}>
        {open && (
          <motion.div
            initial={{ height: 0, opacity: 0 }}
            animate={{ height: 'auto', opacity: 1 }}
            exit={{ height: 0, opacity: 0 }}
            transition={{ duration: 0.3, ease: [0.22, 1, 0.36, 1] }}
            className="overflow-hidden"
          >
            <div className="pb-5 text-sm">{children}</div>
          </motion.div>
        )}
      </AnimatePresence>
    </div>
  )
}
