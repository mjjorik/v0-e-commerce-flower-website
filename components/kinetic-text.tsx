'use client'

import { motion, useReducedMotion } from 'motion/react'
import type { ReactNode } from 'react'

/**
 * Reveals text word-by-word with a slight upward motion + fade.
 */
export function KineticText({
  text,
  className,
  delay = 0,
  stagger = 0.06,
  as = 'span',
  once = true,
}: {
  text: string
  className?: string
  delay?: number
  stagger?: number
  as?: 'span' | 'h1' | 'h2' | 'h3' | 'p'
  once?: boolean
}) {
  const reduce = useReducedMotion()
  const words = text.split(' ')
  const Comp = motion[as as keyof typeof motion] as any

  if (reduce) {
    const Static = as as any
    return <Static className={className}>{text}</Static>
  }

  return (
    <Comp
      className={className}
      initial="hidden"
      whileInView="show"
      viewport={{ once, amount: 0.4 }}
      transition={{ staggerChildren: stagger, delayChildren: delay }}
      aria-label={text}
    >
      {words.map((word, i) => (
        <span key={i} className="inline-block overflow-hidden align-bottom">
          <motion.span
            className="inline-block"
            variants={{
              hidden: { y: '110%' },
              show: {
                y: 0,
                transition: { type: 'spring', stiffness: 200, damping: 24 },
              },
            }}
          >
            {word}
            {i < words.length - 1 ? '\u00A0' : ''}
          </motion.span>
        </span>
      ))}
    </Comp>
  )
}

/** Simple fade + slide up on scroll into view. */
export function Reveal({
  children,
  className,
  delay = 0,
  y = 28,
}: {
  children: ReactNode
  className?: string
  delay?: number
  y?: number
}) {
  const reduce = useReducedMotion()
  return (
    <motion.div
      className={className}
      initial={reduce ? false : { opacity: 0, y }}
      whileInView={{ opacity: 1, y: 0 }}
      viewport={{ once: true, amount: 0.3 }}
      transition={{ duration: 0.7, delay, ease: [0.22, 1, 0.36, 1] }}
    >
      {children}
    </motion.div>
  )
}
