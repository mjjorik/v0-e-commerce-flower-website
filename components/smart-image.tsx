'use client'

import Image, { type ImageProps } from 'next/image'
import { useState } from 'react'
import { cn } from '@/lib/utils'

/**
 * Drop-in replacement for next/image (fill mode) that degrades gracefully.
 * While real photography is missing (404) or fails to load, it reveals a
 * branded botanical gradient instead of a broken-image icon. Once a real
 * asset exists at the given `src`, it loads over the fallback automatically.
 *
 * Use inside a positioned (relative) parent, exactly like <Image fill />.
 */
export function SmartImage({
  src,
  alt,
  className,
  fallbackLabel,
  showMotif = true,
  ...props
}: Omit<ImageProps, 'src'> & {
  src?: string | null
  fallbackLabel?: string
  showMotif?: boolean
}) {
  const [failed, setFailed] = useState(false)
  const usable = typeof src === 'string' && src.length > 0 && !failed

  return (
    <>
      <span
        aria-hidden
        className={cn(
          'img-fallback absolute inset-0 flex items-center justify-center overflow-hidden',
          usable && 'opacity-0',
        )}
      >
        {showMotif && (
          <svg
            viewBox="0 0 100 100"
            className="size-2/5 max-h-32 max-w-32 text-foreground/10"
            fill="none"
            stroke="currentColor"
            strokeWidth="1.25"
            strokeLinecap="round"
          >
            <path d="M50 78 V40" />
            <path d="M50 52 C42 50 36 44 36 36 C44 38 50 44 50 52 Z" />
            <path d="M50 52 C58 50 64 44 64 36 C56 38 50 44 50 52 Z" />
            <g>
              <ellipse cx="50" cy="28" rx="5.5" ry="10" />
              <ellipse cx="50" cy="28" rx="5.5" ry="10" transform="rotate(60 50 28)" />
              <ellipse cx="50" cy="28" rx="5.5" ry="10" transform="rotate(120 50 28)" />
              <circle cx="50" cy="28" r="3" />
            </g>
          </svg>
        )}
        {fallbackLabel && (
          <span className="absolute bottom-3 left-4 font-serif text-sm italic text-foreground/25">
            {fallbackLabel}
          </span>
        )}
      </span>

      {typeof src === 'string' && src.length > 0 && (
        <Image
          src={src}
          alt={alt}
          className={cn(className, failed && 'opacity-0')}
          onError={() => setFailed(true)}
          {...props}
        />
      )}
    </>
  )
}
