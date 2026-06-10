'use client'

import { useEffect, useRef, type ReactNode } from 'react'
import { gsap } from 'gsap'
import { ScrollTrigger } from 'gsap/dist/ScrollTrigger'
import { useGSAP } from '@gsap/react'
import { cn } from '@/lib/utils'

if (typeof window !== 'undefined') {
  gsap.registerPlugin(ScrollTrigger, useGSAP)
}

const ELITE_EASE = "expo.out"

export function KineticText({
  text,
  className,
  as = 'span',
  delay = 0,
}: {
  text: string
  className?: string
  as?: 'span' | 'h1' | 'h2' | 'h3' | 'p'
  delay?: number
}) {
  const Comp = as as any
  const containerRef = useRef<HTMLElement>(null)
  const words = text.split(' ')

  useGSAP(() => {
    if (!containerRef.current) return

    const words = containerRef.current.querySelectorAll('.gsap-word')
    
    gsap.fromTo(words, 
      { 
        y: '110%',
        rotate: 2,
        opacity: 0 
      },
      {
        y: 0,
        rotate: 0,
        opacity: 1,
        duration: 1.2,
        ease: ELITE_EASE,
        stagger: 0.05,
        delay: delay / 1000,
        scrollTrigger: {
          trigger: containerRef.current,
          start: 'top 92%',
          toggleActions: 'play none none none', // Only play once
        }
      }
    )
  }, { scope: containerRef })

  return (
    <Comp ref={containerRef} className={cn('overflow-visible pb-1', className)}>
      {words.map((word, i) => (
        <span key={i} className="inline-block overflow-hidden align-bottom">
          <span className="gsap-word inline-block will-change-transform translate-y-[110%]">
            {word}
            {i < words.length - 1 ? '\u00A0' : ''}
          </span>
        </span>
      ))}
    </Comp>
  )
}

export function Reveal({
  children,
  className,
  delay = 0,
}: {
  children: ReactNode
  className?: string
  delay?: number
}) {
  const ref = useRef<HTMLDivElement>(null)

  useGSAP(() => {
    if (!ref.current) return

    gsap.fromTo(ref.current,
      { 
        opacity: 0, 
        y: 30,
        scale: 0.98
      },
      {
        opacity: 1,
        y: 0,
        scale: 1,
        duration: 1,
        ease: ELITE_EASE,
        delay: delay / 1000,
        scrollTrigger: {
          trigger: ref.current,
          start: 'top 90%',
          toggleActions: 'play none none none',
        }
      }
    )
  }, { scope: ref })

  return (
    <div ref={ref} className={cn('will-change-transform', className)}>
      {children}
    </div>
  )
}
