<style>
    .dt-summary { display: flex; flex-wrap: wrap; gap: 12px 28px; align-items: center; }
    .dt-summary .dt-label { font-size: .72rem; font-weight: 900; letter-spacing: .09em; text-transform: uppercase; color: var(--ink-soft); }
    .dt-summary .dt-value { font-family: var(--font-display); font-size: 1.7rem; line-height: 1.1; }
    .dt-card { display: flex; flex-direction: column; gap: 14px; padding: 18px; border: 2px solid var(--line); border-radius: 20px; background: var(--surface-2); margin-bottom: 16px; }
    .dt-head { display: flex; gap: 14px; align-items: flex-start; flex-wrap: wrap; }
    .dt-head h4 { font-size: 1.25rem; }
    .dt-head .dt-meta { color: var(--ink-soft); font-weight: 800; font-size: .85rem; }
    .dt-score { margin-left: auto; font-family: var(--font-display); font-size: 1.6rem; }
    .dt-words { display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 8px; }
    .dt-word { display: flex; align-items: center; gap: 8px; padding: 9px 12px; border-radius: 14px; border: 2px solid var(--line); background: var(--surface); font-weight: 900; }
    .dt-word i { width: 18px; text-align: center; }
    .dt-word.ok { background: var(--palm-bg); border-color: var(--palm); color: var(--palm-deep); }
    .dt-word.bad { background: var(--coral-bg); border-color: var(--coral); color: var(--coral); }
    .dt-word.missed { background: transparent; border: 2px dashed var(--palm); color: var(--palm-deep); }
    .dt-word.dim { opacity: .5; }
    .dt-legend { font-size: .82rem; font-weight: 800; color: var(--ink-soft); }
</style>
