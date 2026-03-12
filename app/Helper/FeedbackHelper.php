
class FeedbackHelper
{
    public static function getValidationCount()
    {
        return DB::table('feedbacks')
            ->where('fbk_status', 0)
            ->count();
    }

    public static function getActionCount()
    {
        return DB::table('feedbacks')
            ->where('fbk_status', 1)
            ->whereNull('fbk_date_validated') // or use another status field
            ->count();
    }

    public static function getVerificationCount()
    {
        return DB::table('feedbacks')
            ->whereNotNull('fbk_date_validated')
            // Add your verification criteria here
            ->count();
    }
    
    public static function getFeedbacksByBranch()
    {
        return DB::table('feedbacks')
            ->select('branch_id', DB::raw('count(*) as total'))
            ->groupBy('branch_id')
            ->get();
    }
    
    public static function getFeedbacksByType()
    {
        return DB::table('feedbacks')
            ->join('feedback_types', 'feedbacks.typ_id', '=', 'feedback_types.typ_id')
            ->select('feedback_types.typ_value', DB::raw('count(*) as total'))
            ->groupBy('feedback_types.typ_value')
            ->get();
    }
}