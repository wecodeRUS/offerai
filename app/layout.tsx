import type { Metadata } from 'next'
import './globals.css'

export const metadata: Metadata = {
  title: 'OfferAI — подготовка к собеседованию на вашем резюме',
  description: 'Загрузите резюме и вакансию. Агент задаст реальные вопросы, выслушает ответы и разберёт, что стоит изменить. Без шаблонных советов.',
  keywords: 'подготовка к собеседованию, AI, резюме, вакансия, интервью, ATS',
  openGraph: {
    title: 'OfferAI — подготовка к собеседованию',
    description: 'Тренируйте ответы под свою вакансию — не по абстрактным советам',
    url: 'https://getofferai.ru',
    siteName: 'OfferAI',
    locale: 'ru_RU',
    type: 'website',
  },
}

export default function RootLayout({
  children,
}: {
  children: React.ReactNode
}) {
  return (
    <html lang="ru">
      <body>{children}</body>
    </html>
  )
}
