'use client'

import { useState } from 'react'

const faqs = [
  {
    question: 'Чем это отличается от ChatGPT?',
    answer:
      'ChatGPT не знает ничего о вас — ни вашего резюме, ни вакансии. Он даёт общие советы. OfferAI читает оба документа и готовит вопросы именно под вашу ситуацию. Плюс помнит, что вы уже проработали.',
  },
  {
    question: 'Что такое «сессия»?',
    answer:
      'Сессия — это один блок работы: вы отвечаете на вопрос, агент разбирает ответ и даёт рекомендации. Это занимает 5–15 минут. В пакет входит фиксированное число таких блоков.',
  },
  {
    question: 'Откуда агент берёт вопросы?',
    answer:
      'Агент анализирует требования из вакансии, ваш опыт в резюме и базу реальных вопросов, которые задают на аналогичных позициях. Вопросы не генерируются случайно — они привязаны к вашему конкретному контексту.',
  },
  {
    question: 'Как работает система оценки?',
    answer:
      'Каждый ответ оценивается по пяти критериям: конкретность (есть ли цифры и детали), структура (понятна ли логика ответа), попадание в вакансию (соответствует ли требованиям), убедительность и то, насколько видна ваша личная роль. Каждый критерий — от 1 до 5.',
  },
  {
    question: 'Нужна ли карта при регистрации?',
    answer:
      'Нет. Первые 3 сессии бесплатны без ввода платёжных данных. Карта нужна только если вы решите купить платный пакет.',
  },
  {
    question: 'Работает ли для нетехнических специальностей?',
    answer:
      'Да. Агент работает с любыми специальностями — маркетинг, продажи, HR, финансы, менеджмент. Вопросы формируются под вашу вакансию, а не под какую-то конкретную отрасль.',
  },
  {
    question: 'Есть ли голосовой режим?',
    answer:
      'Да, он доступен с тарифа «Подготовка». Вы можете отвечать голосом, как на настоящем интервью. Агент воспринимает речь и разбирает её так же, как текст.',
  },
  {
    question: 'Почему нет подписки?',
    answer:
      'Подготовка к интервью — это конечный процесс. Мы считаем, что подписка здесь не нужна: вы покупаете пакет сессий и используете их, пока готовитесь. Без автосписаний.',
  },
  {
    question: 'Что если я не пройду интервью?',
    answer:
      'Мы не можем гарантировать оффер — слишком много факторов. Агент повышает качество подготовки, но итоговое решение принимает компания. Если хотите — можете пройти ещё один пакет и разобрать, что пошло не так.',
  },
  {
    question: 'Как хранятся мои данные?',
    answer:
      'Серверы в России, соединение по HTTPS. Мы не продаём данные и не используем их для рекламы. Резюме и ответы хранятся только для поддержки прогресса внутри сервиса.',
  },
]

export default function FAQ() {
  const [openIndex, setOpenIndex] = useState<number | null>(null)

  return (
    <section id="faq" className="py-16 sm:py-20 bg-bg">
      <div className="max-w-6xl mx-auto px-4 sm:px-6">
        <div className="max-w-xl mb-10">
          <p className="text-xs font-medium text-accent uppercase tracking-widest mb-3">FAQ</p>
          <h2 className="section-title">Частые вопросы</h2>
        </div>

        <div className="max-w-2xl space-y-1">
          {faqs.map((faq, idx) => (
            <div key={faq.question} className="border border-border rounded-xl overflow-hidden bg-white">
              <button
                className="w-full flex items-center justify-between gap-4 px-5 py-4 text-left"
                onClick={() => setOpenIndex(openIndex === idx ? null : idx)}
              >
                <span className="text-sm font-medium text-text-primary">{faq.question}</span>
                <svg
                  width="16"
                  height="16"
                  fill="none"
                  stroke="currentColor"
                  strokeWidth="2"
                  viewBox="0 0 24 24"
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
    </section>
  )
}
