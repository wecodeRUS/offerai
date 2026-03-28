'use client'

import { useState } from 'react'

const faqs = [
  {
    question: 'Это точно не подписка?',
    answer: 'Точно. Вы покупаете пакет сессий один раз. Ничего не спишется автоматически, карта для пробного периода не нужна.',
  },
  {
    question: 'Работает для нетехнических специальностей?',
    answer: 'Да — маркетинг, продажи, менеджмент, финансы. Вопросы формируются под вашу вакансию, а не под конкретную отрасль.',
  },
  {
    question: 'Можно отвечать голосом?',
    answer: 'С тарифа «Подготовка» — да. Говорите в микрофон, как на реальном интервью. Агент разбирает речь так же, как текст.',
  },
  {
    question: 'Сколько времени занимает подготовка?',
    answer: 'Одна сессия — 5–15 минут. Для базовой подготовки хватает 3–5 сессий. Для основательной — 10–15.',
  },
  {
    question: 'Где хранятся мои данные?',
    answer: 'Серверы в России, соединение по HTTPS. Данные не продаются и не используются для рекламы.',
  },
]

export default function FAQ() {
  const [openIndex, setOpenIndex] = useState<number | null>(null)

  return (
    <section id="faq" className="py-14 sm:py-20 bg-bg">
      <div className="max-w-6xl mx-auto px-4 sm:px-6">
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-10 items-start">
          {/* Left */}
          <div>
            <p className="text-xs font-medium text-accent uppercase tracking-widest mb-3">FAQ</p>
            <h2 className="section-title mb-4">Частые вопросы</h2>
            <p className="text-sm text-text-secondary leading-relaxed mb-6">
              Если не нашли ответ — напишите на <a href="mailto:support@getofferai.ru" className="text-accent hover:underline">support@getofferai.ru</a>
            </p>
          </div>

          {/* Right — accordion */}
          <div className="space-y-2">
            {faqs.map((faq, idx) => (
              <div key={faq.question} className="border border-border rounded-xl overflow-hidden bg-white">
                <button
                  className="w-full flex items-center justify-between gap-4 px-5 py-3.5 text-left"
                  onClick={() => setOpenIndex(openIndex === idx ? null : idx)}
                >
                  <span className="text-sm font-medium text-text-primary">{faq.question}</span>
                  <svg
                    width="16" height="16" fill="none" stroke="currentColor" strokeWidth="2" viewBox="0 0 24 24"
                    className={`flex-shrink-0 text-text-secondary transition-transform duration-200 ${openIndex === idx ? 'rotate-180' : ''}`}
                  >
                    <path strokeLinecap="round" strokeLinejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                  </svg>
                </button>
                {openIndex === idx && (
                  <div className="px-5 pb-4">
                    <p className="text-sm text-text-secondary leading-relaxed">{faq.answer}</p>
                  </div>
                )}
              </div>
            ))}
          </div>
        </div>
      </div>
    </section>
  )
}
