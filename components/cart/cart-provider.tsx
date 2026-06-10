'use client'

import {
  createContext,
  useContext,
  useState,
  useCallback,
  useMemo,
  useEffect,
  type ReactNode,
} from 'react'

export type CartItem = {
  id: string
  productSlug: string
  name: string
  sizeKey: string
  sizeLabel: string
  price: number
  image: string
  imageAlt: string
  quantity: number
  giftMessage?: string
  deliveryDate?: string
  isAddOn?: boolean
}

type ToastMsg = { id: number; text: string }

type CartContextValue = {
  items: CartItem[]
  addItem: (item: Omit<CartItem, 'id' | 'quantity'> & { quantity?: number }) => void
  removeItem: (id: string) => void
  updateQuantity: (id: string, quantity: number) => void
  clearCart: () => void
  isOpen: boolean
  openCart: () => void
  closeCart: () => void
  count: number
  subtotal: number
  toasts: ToastMsg[]
  dismissToast: (id: number) => void
}

export const FREE_DELIVERY_THRESHOLD = 100

const CartContext = createContext<CartContextValue | null>(null)

export function CartProvider({ children }: { children: ReactNode }) {
  const [items, setItems] = useState<CartItem[]>([])
  const [isOpen, setIsOpen] = useState(false)
  const [toasts, setToasts] = useState<ToastMsg[]>([])
  const [isHydrated, setIsHydrated] = useState(false)

  // Hydrate from localStorage
  useEffect(() => {
    const saved = localStorage.getItem("wildflower_cart");
    if (saved) {
      try {
        setItems(JSON.parse(saved));
      } catch (e) {
        console.error("Failed to parse cart", e);
      }
    }
    setIsHydrated(true);
  }, []);

  // Sync to localStorage
  useEffect(() => {
    if (isHydrated) {
      localStorage.setItem("wildflower_cart", JSON.stringify(items));
    }
  }, [items, isHydrated]);

  const pushToast = useCallback((text: string) => {
    const id = Date.now() + Math.random()
    setToasts((t) => [...t, { id, text }])
    setTimeout(() => {
      setToasts((t) => t.filter((x) => x.id !== id))
    }, 3200)
  }, [])

  const dismissToast = useCallback((id: number) => {
    setToasts((t) => t.filter((x) => x.id !== id))
  }, [])

  const addItem: CartContextValue['addItem'] = useCallback(
    (item) => {
      const key = `${item.productSlug}-${item.sizeKey}${item.isAddOn ? '-addon' : ''}`
      setItems((prev) => {
        const existing = prev.find((p) => p.id === key)
        if (existing) {
          return prev.map((p) =>
            p.id === key
              ? { ...p, quantity: p.quantity + (item.quantity ?? 1) }
              : p,
          )
        }
        return [...prev, { ...item, id: key, quantity: item.quantity ?? 1 }]
      })
      pushToast(`${item.name} added to your basket`)
      setIsOpen(true)
    },
    [pushToast],
  )

  const removeItem = useCallback((id: string) => {
    setItems((prev) => prev.filter((p) => p.id !== id))
  }, [])

  const updateQuantity = useCallback((id: string, quantity: number) => {
    setItems((prev) =>
      quantity <= 0
        ? prev.filter((p) => p.id !== id)
        : prev.map((p) => (p.id === id ? { ...p, quantity } : p)),
    )
  }, [])

  const clearCart = useCallback(() => setItems([]), [])
  const openCart = useCallback(() => setIsOpen(true), [])
  const closeCart = useCallback(() => setIsOpen(false), [])

  const count = useMemo(
    () => items.reduce((sum, i) => sum + i.quantity, 0),
    [items],
  )
  const subtotal = useMemo(
    () => items.reduce((sum, i) => sum + i.price * i.quantity, 0),
    [items],
  )

  const value: CartContextValue = {
    items,
    addItem,
    removeItem,
    updateQuantity,
    clearCart,
    isOpen,
    openCart,
    closeCart,
    count,
    subtotal,
    toasts,
    dismissToast,
  }

  return <CartContext.Provider value={value}>{children}</CartContext.Provider>
}

export function useCart() {
  const ctx = useContext(CartContext)
  if (!ctx) throw new Error('useCart must be used within CartProvider')
  return ctx
}
