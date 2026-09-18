@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 focus:border-teal-500 dark:focus:border-teal-400 focus:ring-teal-500 dark:focus:ring-teal-400 rounded-md shadow-sm']) }}>
