# ✅ TODO: Fix Site Display in Admin Pointage Suivi

**Status**: ✅ APPROVED & IN PROGRESS  
**Owner**: BLACKBOXAI  
**Priority**: 🔥 HIGH (User-reported bug)  
**Est. time**: 2 min

## 📋 Breakdown (1 step)

### **Step 1/1**: ✅ **COMPLETED** Edit `resources/views/admin/presence/pointage-suivi.blade.php`

- **Status**: ✅ SUCCESS - Badge logic implemented (green site / gray "À distance")
- **Diff**: Confirmed exact replacement applied

- **Locate**: Site `<td>` → `{{ $event->attendanceDay?->stage?->site?->name ?? 'Hors site' }}`
- **Replace**: Conditional badge logic (students: green site | employees: gray "À distance")
- **Verify**: Test `/admin/presence/pointage-suivi` → badges correctes

---

**Next**: After edit confirmation → `attempt_completion`
